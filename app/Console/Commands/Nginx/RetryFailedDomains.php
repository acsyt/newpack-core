<?php

namespace App\Console\Commands\Nginx;

use App\Jobs\Nginx\CreateTenantDomainJob;
use App\Jobs\Nginx\ConfigureTenantSSLJob;
use App\Models\Central\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:retry-failed {--domain-id= : Specific domain ID to retry} {--dry-run : Show what would be retried without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry failed domain configurations for domains that need SSL setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $specificDomainId = $this->option('domain-id');

        if ($isDryRun) {
            $this->warn('⚠️  Modo simulación activado - No se ejecutarán jobs');
        }

        $this->info('🔄 Buscando dominios que necesitan reintento...');

        $query = Domain::query();

        if ($specificDomainId) {
            $query->where('id', $specificDomainId);
        } else {
            // Buscar dominios que necesitan reintento
            $query->where(function($q) {
                $q->where(function($subQ) {
                    // Dominios creados pero sin SSL
                    $subQ->where('domain_created', true)
                         ->where('domain_configured', false);
                })->orWhere(function($subQ) {
                    // Dominios que fallaron completamente
                    $subQ->where('domain_created', false)
                         ->where('domain_configured', false)
                         ->where('created_at', '>', now()->subHours(24)); // Solo últimas 24h
                });
            });
        }

        $domainsToRetry = $query->with('tenant')->get();

        if ($domainsToRetry->isEmpty()) {
            $this->info('✅ No se encontraron dominios que necesiten reintento');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info("Encontrados {$domainsToRetry->count()} dominios para reintentar:");

        $tableData = $domainsToRetry->map(function ($domain) {
            $status = $this->getDomainStatus($domain);
            return [
                'ID' => $domain->id,
                'Dominio' => $domain->domain,
                'Tenant' => $domain->tenant->name ?? 'Sin tenant',
                'Creado' => $domain->domain_created ? '✅' : '❌',
                'Configurado' => $domain->domain_configured ? '✅' : '❌',
                'Estado' => $status,
                'Creado el' => $domain->created_at->format('Y-m-d H:i'),
            ];
        })->toArray();

        $this->table(['ID', 'Dominio', 'Tenant', 'Creado', 'Configurado', 'Estado', 'Creado el'], $tableData);

        if ($isDryRun) {
            $this->newLine();
            $this->info('💡 Para ejecutar los reintentos, ejecuta sin --dry-run');
            return Command::SUCCESS;
        }

        $this->newLine();
        if (!$this->confirm('¿Proceder con el reintento de estos dominios?')) {
            $this->info('❌ Operación cancelada por el usuario');
            return Command::SUCCESS;
        }

        $this->retryDomains($domainsToRetry);

        return Command::SUCCESS;
    }

    private function getDomainStatus(Domain $domain): string
    {
        if (!$domain->domain_created && !$domain->domain_configured) {
            return 'Creación fallida';
        }
        if ($domain->domain_created && !$domain->domain_configured) {
            return 'SSL pendiente';
        }
        if ($domain->domain_created && $domain->domain_configured) {
            return 'Configurado';
        }
        return 'Estado desconocido';
    }

    private function retryDomains($domains): void
    {
        $this->newLine();
        $this->info('🚀 Iniciando reintentos...');

        $progressBar = $this->output->createProgressBar($domains->count());
        $progressBar->start();

        $retried = 0;

        foreach ($domains as $domain) {
            try {
                if (!$domain->domain_created) {
                    // Reintentar creación de dominio
                    CreateTenantDomainJob::dispatch($domain);
                    $this->newLine();
                    $this->line("   ⏳ Reintentando creación: {$domain->domain}");
                } elseif (!$domain->domain_configured) {
                    // Reintentar SSL
                    ConfigureTenantSSLJob::dispatch($domain);
                    $this->newLine();
                    $this->line("   🔒 Reintentando SSL: {$domain->domain}");
                }

                Log::info("Dominio enviado para reintento: {$domain->domain}", [
                    'domain_id' => $domain->id,
                    'domain_created' => $domain->domain_created,
                    'domain_configured' => $domain->domain_configured,
                    'retry_type' => !$domain->domain_created ? 'creation' : 'ssl',
                ]);

                $retried++;

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("   ❌ Error reintentando {$domain->domain}: {$e->getMessage()}");
                Log::error("Error reintentando dominio", [
                    'domain' => $domain->domain,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ {$retried} dominios enviados para reintento");
        $this->line('💡 Los jobs se ejecutarán en background. Monitorea los logs para ver el progreso.');
        $this->line('📊 Usa "php artisan nginx:sync-domains" en unos minutos para verificar resultados.');
    }
}
