<?php

namespace App\Console\Commands\Nginx;

use App\Models\Central\Domain;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Comando para sincronizar dominios entre nginx proxy manager y la base de datos local
 *
 * Estructura de respuesta de nginx:
 * [
 *     {
 *         id: 51,
 *         created_on: "2025-08-04 00:38:01",
 *         modified_on: "2025-08-04 00:38:16",
 *         owner_user_id: 1,
 *         domain_names: [
 *            "4s.amartinez.qa.acsyt.com"
 *         ],
 *         forward_host: "amartinez-client",
 *         forward_port: 4173,
 *         access_list_id: 0,
 *         certificate_id: 52,
 *         ssl_forced: false,
 *         caching_enabled: false,
 *         block_exploits: false,
 *         advanced_config: "",
 *         meta: {
 *             letsencrypt_agree: true,
 *             dns_challenge: false,
 *             letsencrypt_email: "dev@acsyt.com",
 *             nginx_online: true,
 *             nginx_err: null
 *         },
 *         allow_websocket_upgrade: false,
 *         http2_support: false,
 *         forward_scheme: "http",
 *         enabled: true,
 *         locations: [ ],
 *         hsts_enabled: false,
 *         hsts_subdomains: false
 *    },
 * ]
 */
class SyncNginxDomains extends Command
{
    protected $signature = 'nginx:sync-domains {--dry-run : Ejecutar en modo simulación sin hacer cambios}';

    protected $description = 'Sincroniza dominios entre nginx proxy manager y la base de datos local';

    public function handle()
    {
        $this->info('🚀 Iniciando sincronización de dominios...');

        $startTime = microtime(true);
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('⚠️  Modo simulación activado - No se realizarán cambios');
        }

        $stats = [
            'updated' => 0,
            'marked_unconfigured' => 0,
            'errors' => 0,
            'warnings' => 0,
            'desynchronization_detected' => 0,
            'domains_needing_retry' => 0
        ];

        try {
            $localDomains = Domain::all()->keyBy('domain');

            $this->info("📊 Dominios encontrados en BD local: {$localDomains->count()}");
            $this->newLine();

            $this->processLocalDomainsWithVerification($localDomains, $stats, $isDryRun);

            $this->checkDomainsNeedingRetry($stats, $isDryRun);

            $this->displaySummary($stats, $startTime, $isDryRun);

            return $stats['errors'] > 0 ? Command::FAILURE : Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('💥 Error crítico en sincronización: ' . $e->getMessage());

            Log::error('Error crítico en comando nginx:sync-domains', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }


    private function processLocalDomainsWithVerification(Collection $localDomains, array &$stats, bool $isDryRun): void {
        $this->info('🔍 Verificando dominios locales contra NPM...');

        $progressBar = $this->output->createProgressBar($localDomains->count());
        $progressBar->start();

        foreach ($localDomains as $domainName => $localDomain) {
            try {
                $this->verifyAndUpdateSingleDomain($localDomain, $domainName, $stats, $isDryRun);
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->newLine();
                $this->error("❌ Error verificando dominio {$domainName}: {$e->getMessage()}");

                Log::error('Error verificando dominio individual', [
                    'domain' => $domainName,
                    'domain_id' => $localDomain->id,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    private function verifyAndUpdateSingleDomain(Domain $localDomain, string $domainName, array &$stats, bool $isDryRun): void {
        $client = new Client([
            'base_uri' => env('NPM_MS_URL', 'http://npm-ms:3000'),
            'timeout' => 15.0,
        ]);

        try {
            $response = $client->get("/api/nginx/verify-domain/{$domainName}");
            $verification = json_decode($response->getBody()->getContents(), true);

            if (!$verification) {
                throw new \Exception('Respuesta de verificación inválida');
            }

            $this->updateDomainBasedOnVerification($localDomain, $verification, $domainName, $stats, $isDryRun);

        } catch (RequestException $e) {
            // Si NPM no responde, asumir que el dominio no existe
            $verification = [
                'exists' => false,
                'configured' => false,
                'hasSSL' => false,
                'hasCertificate' => false,
                'sslForced' => false,
            ];

            Log::warning("NPM no disponible para verificar {$domainName}, asumiendo no existe", [
                'error' => $e->getMessage()
            ]);

            $this->updateDomainBasedOnVerification($localDomain, $verification, $domainName, $stats, $isDryRun);
        }
    }

    private function updateDomainBasedOnVerification(Domain $localDomain, array $verification, string $domainName, array &$stats, bool $isDryRun): void {
        $needsUpdate = false;
        $updates = [];
        $changes = [];

        // Verificar domain_created
        if (!$localDomain->domain_created && $verification['exists']) {
            $updates['domain_created'] = true;
            $changes[] = 'domain_created: false → true (DESINCRONIZACIÓN DETECTADA)';
            $needsUpdate = true;
        } elseif ($localDomain->domain_created && !$verification['exists']) {
            $updates['domain_created'] = false;
            $changes[] = 'domain_created: true → false (dominio perdido en NPM)';
            $needsUpdate = true;
        }

        // Verificar domain_configured (debe tener dominio Y certificado)
        $shouldBeConfigured = $verification['exists'] &&
                             $verification['configured'] &&
                             $verification['hasCertificate'];

        if ($localDomain->domain_configured !== $shouldBeConfigured) {
            $updates['domain_configured'] = $shouldBeConfigured;
            $status = $shouldBeConfigured ? 'true' : 'false';
            $oldStatus = $localDomain->domain_configured ? 'true' : 'false';
            $changes[] = "domain_configured: {$oldStatus} → {$status}";
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            try {
                if (!$isDryRun) {
                    $localDomain->update($updates);
                }

                $stats['updated']++;
                $dryRunPrefix = $isDryRun ? '[SIMULACIÓN] ' : '';

                // Log detallado de cambios
                Log::info("{$dryRunPrefix}Dominio sincronizado: {$domainName}", [
                    'changes' => $changes,
                    'npm_verification' => $verification,
                    'local_before' => [
                        'domain_created' => $localDomain->domain_created,
                        'domain_configured' => $localDomain->domain_configured,
                    ],
                    'updates_applied' => $updates
                ]);

                // Detectar casos críticos de desincronización
                if (isset($updates['domain_created'])) {
                    if ($updates['domain_created']) {
                        $this->warn("🚨 DESINCRONIZACIÓN: {$domainName} existe en NPM pero BD decía que no");
                    } else {
                        $this->warn("🚨 DOMINIO PERDIDO: {$domainName} marcado como creado en BD pero no existe en NPM");
                    }
                    $stats['desynchronization_detected']++;
                }

                if (isset($updates['domain_configured']) && $verification['exists'] && !$verification['hasCertificate'] && $localDomain->domain_created) {
                    $stats['domains_needing_retry']++;
                }

            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Error actualizando dominio {$domainName}", [
                    'error' => $e->getMessage(),
                    'domain_id' => $localDomain->id,
                    'attempted_updates' => $updates
                ]);
                throw $e;
            }
        } else {
            // Log de estado sincronizado
            Log::debug("Dominio sincronizado: {$domainName}", [
                'local_state' => [
                    'domain_created' => $localDomain->domain_created,
                    'domain_configured' => $localDomain->domain_configured,
                ],
                'npm_state' => $verification,
                'status' => 'no changes needed'
            ]);
        }
    }

    private function checkDomainsNeedingRetry(array &$stats, bool $isDryRun): void {
        $this->info('🔄 Verificando dominios que necesitan reintento...');

        // Buscar dominios que están creados pero no configurados (posible fallo de SSL)
        $domainsNeedingRetry = Domain::where('domain_created', true)
            ->where('domain_configured', false)
            ->get();

        if ($domainsNeedingRetry->isEmpty()) {
            $this->line('   • No se encontraron dominios que necesiten reintento');
            return;
        }

        $stats['domains_needing_retry'] = $domainsNeedingRetry->count();
        $this->line("   • Encontrados {$domainsNeedingRetry->count()} dominios que necesitan reintento de SSL");

        foreach ($domainsNeedingRetry as $domain) {
            Log::info("Dominio necesita reintento de SSL: {$domain->domain}", [
                'domain_id' => $domain->id,
                'tenant_id' => $domain->tenant_id,
                'domain_created' => $domain->domain_created,
                'domain_configured' => $domain->domain_configured,
                'created_at' => $domain->created_at,
                'suggestion' => 'Considerar reintento manual con ConfigureTenantSSLJob'
            ]);

            $this->warn("⏳ {$domain->domain} - dominio creado pero SSL pendiente");
        }

        if (!$isDryRun && $domainsNeedingRetry->count() > 0) {
            $this->newLine();
            $this->info('💡 Para reintentar SSL en dominios específicos, usa:');
            $this->line('   php artisan queue:retry --queue=default');
            $this->line('   o crea jobs específicos con ConfigureTenantSSLJob');
        }
    }

    private function displaySummary(array $stats, float $startTime, bool $isDryRun): void {
        $executionTime = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $modeText = $isDryRun ? ' (Modo simulación)' : '';
        $this->info("📋 Resumen de sincronización{$modeText}:");
        $this->line("   • Dominios actualizados: {$stats['updated']}");
        $this->line("   • Dominios marcados como no configurados: {$stats['marked_unconfigured']}");
        $this->line("   • Desincronizaciones críticas detectadas: {$stats['desynchronization_detected']}");
        $this->line("   • Dominios que necesitan reintento SSL: {$stats['domains_needing_retry']}");
        $this->line("   • Advertencias: {$stats['warnings']}");
        $this->line("   • Errores: {$stats['errors']}");
        $this->line("   • Tiempo de ejecución: {$executionTime}s");

        if ($stats['errors'] === 0) {
            $this->info('✅ Sincronización completada exitosamente');
        } else {
            $this->warn("⚠️  Sincronización completada con {$stats['errors']} errores");
        }

        Log::info('Sincronización de dominios completada', [
            'stats' => $stats,
            'execution_time' => $executionTime,
            'dry_run' => $isDryRun
        ]);
    }

}
