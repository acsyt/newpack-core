<?php

namespace App\Jobs\Nginx;

use App\Models\Central\Domain;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class ConfigureTenantSSLJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 60; // 1 minuto entre reintentos (SSL necesita más tiempo)

    protected Domain $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function middleware(): array
    {
        return [
            new WithoutOverlapping("configure-ssl-{$this->domain->id}")
        ];
    }

    public function handle(): void
    {
        if (!env('CONFIGURE_DOMAIN', false)) {
            Log::info("Configuración de SSL deshabilitada", [
                'domain' => $this->domain->domain
            ]);
            return;
        }

        // Verificar que el dominio esté creado primero
        if (!$this->domain->domain_created) {
            Log::error("No se puede configurar SSL: dominio no creado", [
                'domain' => $this->domain->domain,
                'tenant_id' => $this->domain->tenant_id
            ]);
            return;
        }

        Log::info("Iniciando configuración de SSL", [
            'domain' => $this->domain->domain,
            'tenant_id' => $this->domain->tenant_id
        ]);

        // Verificar si el dominio ya tiene SSL configurado
        if ($this->checkSSLAlreadyConfigured()) {
            Log::info("SSL ya está configurado, job completado exitosamente", [
                'domain' => $this->domain->domain,
                'tenant_id' => $this->domain->tenant_id,
                'action' => 'No se requiere configuración adicional de SSL'
            ]);
            return;
        }

        $this->configureSSLInNPM();

        // Esperar un momento para que NPM propague el certificado internamente
        Log::info("Esperando propagación de certificado SSL", [
            'domain' => $this->domain->domain,
            'delay' => '10 segundos'
        ]);
        sleep(10);

        // Verificar que el SSL se haya configurado correctamente
        $this->verifySSLConfiguration();
    }

    private function configureSSLInNPM(): void
    {
        if(env('APP_ENV') === 'production') {
            $client = new Client([
                'base_uri' => env('NPM_MS_URL', 'http://npm-ms:3000'),
                'timeout'  => 60.0, // Más tiempo para SSL
            ]);

            try {
                $response = $client->post('/api/nginx/tenant/ssl', [
                    'json' => [
                        'domainName'    => $this->domain->domain,
                        'email'         => "dev@acsyt.com",
                    ],
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ]
                ]);

                $responseBody = json_decode($response->getBody()->getContents(), true);

                Log::info("SSL configurado exitosamente", [
                    'domain'        => $this->domain->domain,
                    'tenant_id'     => $this->domain->tenant_id,
                    'response'      => $responseBody
                ]);

                $this->domain->update(['domain_configured' => true]);

                Log::info("Dominio marcado como completamente configurado", [
                    'domain'        => $this->domain->domain,
                    'tenant_id'     => $this->domain->tenant_id
                ]);

                // Trigger sync after SSL configuration
                \Illuminate\Support\Facades\Artisan::call('nginx:sync-domains');

            } catch (RequestException $e) {
                $errorMessage = $e->getMessage();
                $responseBody = null;

                if ($e->hasResponse()) {
                    $responseBody = $e->getResponse()->getBody()->getContents();
                }

                Log::error("Error configurando SSL en NPM", [
                    'domain'        => $this->domain->domain,
                    'tenant_id'     => $this->domain->tenant_id,
                    'error'         => $errorMessage,
                    'response_body' => $responseBody,
                    'status_code'   => $e->getCode()
                ]);

                // Verificar si es rate limit de Let's Encrypt
                if (str_contains($errorMessage, 'too many certificates') ||
                    str_contains($errorMessage, 'rate-limits') ||
                    str_contains($responseBody ?? '', 'too many certificates')) {

                    Log::warning("Rate limit de Let's Encrypt detectado", [
                        'domain' => $this->domain->domain,
                        'mensaje' => 'Demasiados certificados creados, esperar hasta que expire el rate limit',
                        'accion' => 'Marcando como no configurado para reintento posterior'
                    ]);

                    // Marcar como no configurado pero no fallar el job
                    $this->domain->update(['domain_configured' => false]);
                    return; // No re-lanzar excepción para rate limits
                }

                // No marcar domain_created como false, solo SSL como false
                $this->domain->update(['domain_configured' => false]);

                // Re-lanzar para retry, pero solo del SSL
                throw $e;
            }
        } else {
            $this->domain->update(['domain_configured' => true]);
            Log::info("SSL marcado como configurado (desarrollo)", [
                'domain'        => $this->domain->domain,
                'tenant_id'     => $this->domain->tenant_id
            ]);
        }
    }

    private function verifySSLConfiguration(): void
    {
        if(env('APP_ENV') !== 'production') {
            Log::info("Verificación de SSL omitida (desarrollo)", [
                'domain' => $this->domain->domain
            ]);
            return;
        }

        $client = new Client([
            'base_uri' => env('NPM_MS_URL', 'http://npm-ms:3000'),
            'timeout'  => 15.0,
        ]);

        try {
            $response = $client->get("/api/nginx/verify-domain/{$this->domain->domain}");
            $verification = json_decode($response->getBody()->getContents(), true);

            if (!$verification['exists']) {
                Log::error("Verificación fallida: dominio no existe para SSL", [
                    'domain' => $this->domain->domain,
                    'verification' => $verification
                ]);
                throw new \Exception("Dominio no encontrado para configuración SSL");
            }

            if (!$verification['hasCertificate']) {
                Log::error("Verificación fallida: certificado SSL no encontrado", [
                    'domain' => $this->domain->domain,
                    'verification' => $verification
                ]);
                throw new \Exception("Certificado SSL no se configuró correctamente en NPM");
            }

            Log::info("Verificación exitosa: SSL configurado correctamente", [
                'domain' => $this->domain->domain,
                'verification' => $verification
            ]);

            // Actualizar estado basado en verificación real
            $this->domain->update([
                'domain_configured' => $verification['configured'] && $verification['hasCertificate']
            ]);

        } catch (RequestException $e) {
            Log::warning("Error verificando SSL (reintentará)", [
                'domain' => $this->domain->domain,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);
            throw $e; // Re-lanzar para que se reintente
        }
    }

    private function checkSSLAlreadyConfigured(): bool
    {
        if(env('APP_ENV') !== 'production') {
            Log::info("Verificación de SSL existente omitida (desarrollo)", [
                'domain' => $this->domain->domain
            ]);
            return false; // En desarrollo, permitir siempre la configuración
        }

        $client = new Client([
            'base_uri' => env('NPM_MS_URL', 'http://npm-ms:3000'),
            'timeout'  => 15.0,
        ]);

        try {
            $response = $client->get("/api/nginx/verify-domain/{$this->domain->domain}");
            $verification = json_decode($response->getBody()->getContents(), true);

            if (!$verification['exists']) {
                Log::error("Dominio no existe para configuración SSL", [
                    'domain' => $this->domain->domain,
                    'verification' => $verification,
                    'action' => 'El dominio debe crearse primero'
                ]);
                return true; // Evitar configurar SSL si el dominio no existe
            }

            if ($verification['hasCertificate'] && $verification['configured']) {
                Log::info("SSL ya está configurado correctamente", [
                    'domain' => $this->domain->domain,
                    'verification' => $verification,
                    'action' => 'Actualizando estado local'
                ]);

                // Actualizar el estado local basado en el estado real
                $this->domain->update([
                    'domain_created' => true,
                    'domain_configured' => true
                ]);

                return true; // SSL ya está configurado
            }

            Log::info("SSL no está configurado, procediendo con configuración", [
                'domain' => $this->domain->domain,
                'verification' => $verification
            ]);

            return false; // SSL no está configurado, se puede proceder

        } catch (RequestException $e) {
            Log::warning("Error verificando SSL existente, procediendo con configuración", [
                'domain' => $this->domain->domain,
                'error' => $e->getMessage()
            ]);
            return false; // En caso de error, permitir la configuración
        }
    }
}
