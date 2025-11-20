<?php

namespace App\Jobs\Nginx;

use App\Models\Central\Domain;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class CreateTenantDomainJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 30; // 30 segundos entre reintentos

    protected Domain $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function middleware(): array
    {
        return [
            new WithoutOverlapping("create-domain-{$this->domain->id}")
        ];
    }

    public function handle(): void
    {
        if (!env('CONFIGURE_DOMAIN', false)) {
            Log::info("Configuración de dominio deshabilitada", [
                'domain' => $this->domain->domain
            ]);
            return;
        }

        Log::info("Iniciando creación de dominio (sin SSL)", [
            'domain' => $this->domain->domain,
            'tenant_id' => $this->domain->tenant_id
        ]);

        // Verificar si el dominio ya existe antes de crearlo
        if ($this->checkDomainExists()) {
            Log::info("Dominio ya existe, job completado exitosamente", [
                'domain' => $this->domain->domain,
                'tenant_id' => $this->domain->tenant_id,
                'action' => 'No se requiere creación adicional'
            ]);
            return;
        }

        $this->createDomainInNPM();

        // Verificar que el dominio se haya creado correctamente
        $this->verifyDomainCreation();
    }

    private function createDomainInNPM(): void
    {
        if(env('APP_ENV') === 'production') {
            $client = new Client([
                'base_uri' => env('NPM_MS_URL', 'http://npm-ms:3000'),
                'timeout'  => 30.0,
            ]);

            try {
                $response = $client->post('/api/nginx/tenant/domain', [
                    'json' => [
                        'targetHost'    => 'amartinez-client',
                        'subdomain'     => $this->domain->domain,
                        'targetPort'    => "4173",
                        'email'         => "dev@acsyt.com",
                    ],
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ]
                ]);

                $responseBody = json_decode($response->getBody()->getContents(), true);

                Log::info("Dominio creado exitosamente en NPM (sin SSL)", [
                    'domain'        => $this->domain->domain,
                    'tenant_id'     => $this->domain->tenant_id,
                    'response'      => $responseBody
                ]);

                $this->domain->update([
                    'domain_configured' => false, // Aún no tiene SSL
                    'domain_created' => true      // Pero el dominio está creado
                ]);

                Log::info("Dominio marcado como creado (pendiente SSL)", [
                    'domain'        => $this->domain->domain,
                    'tenant_id'     => $this->domain->tenant_id
                ]);

            } catch (RequestException $e) {
                Log::error("Error creando dominio en NPM", [
                    'domain'        => $this->domain->domain,
                    'tenant_id'     => $this->domain->tenant_id,
                    'error'         => $e->getMessage()
                ]);

                $this->domain->update([
                    'domain_configured' => false,
                    'domain_created' => false
                ]);
                throw $e; // Re-lanzar para que el job falle y se reintente
            }
        } else {
            $this->domain->update([
                'domain_configured' => false,
                'domain_created' => true
            ]);
            Log::info("Dominio marcado como creado (desarrollo)", [
                'domain'        => $this->domain->domain,
                'tenant_id'     => $this->domain->tenant_id
            ]);
        }
    }

    private function verifyDomainCreation(): void
    {
        if(env('APP_ENV') !== 'production') {
            Log::info("Verificación de dominio omitida (desarrollo)", [
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
                Log::error("Verificación fallida: dominio no existe en NPM", [
                    'domain' => $this->domain->domain,
                    'verification' => $verification,
                    'issue' => 'Posible fallo en la creación o dominio perdido en NPM',
                    'attempt' => $this->attempts()
                ]);

                $this->domain->update(['domain_created' => false]);
                throw new \Exception("Dominio no encontrado en NPM después de la creación - reintentando...");
            }

            Log::info("Verificación exitosa: dominio existe en NPM", [
                'domain' => $this->domain->domain,
                'verification' => $verification
            ]);

            // Actualizar estado basado en verificación real
            $this->domain->update([
                'domain_created' => true,
                'domain_configured' => $verification['configured'] && $verification['hasCertificate']
            ]);

        } catch (RequestException $e) {
            Log::warning("Error verificando dominio (no crítico)", [
                'domain' => $this->domain->domain,
                'error' => $e->getMessage()
            ]);
            // No lanzar excepción para verification failures
        }
    }

    private function checkDomainExists(): bool
    {
        if(env('APP_ENV') !== 'production') {
            Log::info("Verificación de dominio existente omitida (desarrollo)", [
                'domain' => $this->domain->domain
            ]);
            return false; // En desarrollo, permitir siempre la creación
        }

        $client = new Client([
            'base_uri' => env('NPM_MS_URL', 'http://npm-ms:3000'),
            'timeout'  => 15.0,
        ]);

        try {
            $response = $client->get("/api/nginx/verify-domain/{$this->domain->domain}");
            $verification = json_decode($response->getBody()->getContents(), true);

            if ($verification['exists']) {
                Log::warning("Dominio ya existe en NPM", [
                    'domain' => $this->domain->domain,
                    'verification' => $verification,
                    'action' => 'Actualizando estado local basado en estado real'
                ]);

                // Actualizar el estado local basado en el estado real del dominio
                $this->domain->update([
                    'domain_created' => true,
                    'domain_configured' => $verification['configured'] && $verification['hasCertificate']
                ]);

                return true; // El dominio ya existe
            }

            Log::info("Dominio no existe en NPM, procediendo con creación", [
                'domain' => $this->domain->domain
            ]);

            return false; // El dominio no existe, se puede crear

        } catch (RequestException $e) {
            Log::warning("Error verificando existencia de dominio, procediendo con creación", [
                'domain' => $this->domain->domain,
                'error' => $e->getMessage()
            ]);
            return false; // En caso de error, permitir la creación
        }
    }
}
