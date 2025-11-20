<?php

namespace App\Console\Commands\Views;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class RefreshCentralViewCommand extends Command {
    abstract protected function getTableName(): string;
    abstract protected function getViewName(): string;

    protected function getCommonColumns($dbName, $tableName): array
    {
        try {
            $columns = DB::connection('mysql')->select("DESCRIBE `{$dbName}`.`{$tableName}`");
            return array_map(function($col) {
                return $col->Field;
            }, $columns);
        } catch (\Exception $e) {
            Log::info( $e->getMessage() );
            return [];
        }
    }

    public function handle() {
        $viewName  = $this->getViewName();
        $tableName = $this->getTableName();

        $this->info("Refreshing {$viewName} view...");

        $tenants = Tenant::query()
            ->select(['id', 'property_id'])
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.tenancy_db_name")) AS tenancy_db_name')
            ->whereRaw('JSON_EXTRACT(data, "$.tenancy_db_name") IS NOT NULL')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.tenancy_db_name")) <> ""')
            ->toBase()   // evita instanciar Eloquent Models
            ->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found with a valid tenancy_db_name.');
            return 0;
        }

        // Log resumido (evita spam en loops)
        $this->info("Table: " . $tenants->pluck('tenancy_db_name')->implode(', '));

        try {
            // 2) Busca columnas comunes con el primer tenant válido que responda
            $commonColumns = [];
            foreach ($tenants as $t) {
                $dbName = (string) ($t->tenancy_db_name ?? '');
                if ($dbName === '') continue;

                $this->info("Checking columns on tenant DB: {$dbName}");
                $cols = $this->getCommonColumns($dbName, $tableName);
                if (!empty($cols)) {
                    $commonColumns = $cols;
                    break;
                }
            }

            if (empty($commonColumns)) {
                $this->warn('No common columns found across tenants.');
                return 0;
            }

            // 3) Preparación segura de identificadores
            $escapeIdent = static function (string $ident): string {
                // Permite solo [A-Za-z0-9_]; rechaza otros (guarda contra inyección en identificadores)
                if (!preg_match('/^[A-Za-z0-9_]+$/', $ident)) {
                    throw new \RuntimeException("Invalid identifier: {$ident}");
                }
                return "`{$ident}`";
            };

            $columnsList = implode(', ', array_map(
                fn ($col) => 't.' . $escapeIdent($col),
                $commonColumns
            ));

            $safeTable = $escapeIdent($tableName);
            $unions = [];

            // 4) Construye UNION ALL sólo con tenants válidos y ya filtrados
            foreach ($tenants as $t) {
                $dbName = (string) ($t->tenancy_db_name ?? '');
                if ($dbName === '') continue;

                // valida identificador de base de datos
                $safeDb = $escapeIdent($dbName);

                $tenantId   = (int) $t->id;
                $propertyId = (int) $t->property_id;

                $unions[] = "SELECT {$columnsList}, {$tenantId} AS tenant_id, {$propertyId} AS property_id
                            FROM {$safeDb}.{$safeTable} t";
            }

            if (empty($unions)) {
                $this->warn('No tenants produced a valid SELECT for the view.');
                return 0;
            }

            $unionQueries = implode(' UNION ALL ', $unions);

            $safeView = $escapeIdent($viewName);

            // 5) Crea/Reemplaza la vista
            DB::connection('mysql')->statement("CREATE OR REPLACE VIEW {$safeView} AS {$unionQueries}");

            $this->info("✅ {$viewName} view refreshed successfully!");
            return 0;

        } catch (\Throwable $e) {
            Log::info( $e->getMessage() );
            $this->error("❌ Error refreshing {$viewName}: " . $e->getMessage());
            return 1;
        }
    }


}
