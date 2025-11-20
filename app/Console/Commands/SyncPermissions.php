<?php

namespace App\Console\Commands;

use App\Models\Shared\Role;
use App\Models\Shared\Permission;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los permisos con la base de datos';

    private $roles;

    private $allPermissions;

    private $moduleOrderCentral = [
        'dashboard',
        'users',
        'roles',
    ];

    private $moduleOrderTenant = [
        'dashboard',
        'roles',
        'users'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->roles = collect([
            ['name' => 'admin',             'description' => 'Administrator'],
        ]);

        $this->allPermissions = collect([
            // 1. Dashboard
            ['name' => 'dashboard.index',   'description' => 'Ingresar a módulo',   'roles' => ['admin'], 'connection' => ['central', 'tenant']],

            ['name' => 'users.index',           'description' => 'Listar usuarios',                        'roles' => ['admin'], 'connection' => ['central', 'tenant']],
            ['name' => 'users.create',          'description' => 'Crear usuario',                          'roles' => ['admin'], 'connection' => ['central', 'tenant']],
            ['name' => 'users.export',          'description' => 'Exportar listado de usuarios',           'roles' => ['admin'], 'connection' => ['central', 'tenant']],
            ['name' => 'users.show',            'description' => 'Ver detalles de usuario',                'roles' => ['admin'], 'connection' => ['central', 'tenant']],
            ['name' => 'users.edit',            'description' => 'Editar usuario',                         'roles' => ['admin'], 'connection' => ['central', 'tenant']],
            ['name' => 'users.change-password', 'description' => 'Cambiar contraseña de usuario',         'roles' => ['admin'], 'connection' => ['central', 'tenant']],
        ]);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Sincronizando permisos en base de datos central...');
            $this->syncDatabase();
            $this->info('Permisos sincronizados en central correctamente.');

            $tenants = Tenant::all();

            if ($tenants->isNotEmpty()) {
                $this->info('Sincronizando permisos en tenants...');
                $bar = $this->output->createProgressBar(count($tenants));
                $bar->start();

                foreach ($tenants as $tenant) {
                    $tenant->run(function () use ($tenant) {
                        $this->syncDatabase($tenant);
                    });
                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();
                $this->info('Permisos sincronizados en todos los tenants correctamente.');
            } else {
                $this->info('No se encontraron tenants para sincronizar.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error durante la sincronización: {$e->getMessage()}");
            return 1;
        }
    }

    private function syncDatabase($tenant = null)
    {
        DB::transaction(function () use ($tenant) {
            foreach ($this->roles as $role) {
                Role::updateOrCreate([
                    'name'        => $role['name'],
                    'guard_name'  => 'web',
                ], [
                    'description' => $role['description'] ?? '',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            $filteredPermissions = $this->allPermissions->filter(function ($permission) use ($tenant) {
                $connection = $permission['connection'] ?? [];
                if ($tenant === null) return in_array('central', $connection);
                return in_array('tenant', $connection);
            });

            // Obtener los nombres de permisos del arreglo para esta conexión
            $permissionNames = $filteredPermissions->pluck('name')->toArray();

            // Eliminar permisos que están en la DB pero no en el arreglo del sistema
            $deletedCount = Permission::where('guard_name', 'web')
                ->whereNotIn('name', $permissionNames)
                ->delete();

            if ($deletedCount > 0) {
                $this->info("Eliminados {$deletedCount} permisos que no están en el arreglo del sistema");
            }

            // Aplicar orden personalizado según el tipo de conexión
            $orderedPermissions = $this->applyCustomOrder($filteredPermissions, $tenant);

            // Crear o actualizar permisos del arreglo
            foreach ($orderedPermissions as $index => $permission) {
                $existingPermission = Permission::where('name', $permission['name'])
                    ->where('guard_name', 'web')
                    ->first();

                if ($existingPermission) {
                    // Actualizar descripción y orden si son diferentes
                    $needsUpdate = false;
                    $updateData = [];

                    if ($existingPermission->description !== ($permission['description'] ?? '')) {
                        $updateData['description'] = $permission['description'] ?? '';
                        $needsUpdate = true;
                    }

                    if ($existingPermission->order !== $index) {
                        $updateData['order'] = $index;
                        $needsUpdate = true;
                    }

                    if ($needsUpdate) {
                        $existingPermission->update($updateData);
                        $this->info("Actualizado permiso: {$permission['name']} (orden: {$index})");
                    } else {
                        $this->info("Permiso existente: {$permission['name']} - sin cambios");
                    }
                } else {
                    $this->info("Creando nuevo permiso: {$permission['name']} (orden: {$index})");
                    Permission::create([
                        'name'        => $permission['name'],
                        'guard_name'  => 'web',
                        'description' => $permission['description'] ?? '',
                        'order'       => $index,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }

            $this->assignPermissionsToRoles($filteredPermissions);
        });
    }

    private function syncPermissions(Role $role, array $newPermissions)
    {
        if (empty($newPermissions)) return;

        $permissions = Permission::whereIn('name', $newPermissions)
            ->where('guard_name', 'web')
            ->get();

        $permissionNames = $permissions->pluck('name')->toArray();
        $missingPermissions = array_diff($newPermissions, $permissionNames);

        if (!empty($missingPermissions)) {
            throw new \Exception("Permisos no encontrados: " . implode(', ', $missingPermissions));
        }

        $role->syncPermissions($permissions);
        $this->info("Sincronizados " . count($permissions) . " permisos para rol: {$role->name}");
    }

    private function assignPermissionsToRoles($filteredPermissions)
    {
        $rolesToPermissions = [];

        foreach ($filteredPermissions as $permission) {
            foreach ($permission['roles'] ?? [] as $roleName) {
                $rolesToPermissions[$roleName][] = $permission['name'];
            }
        }

        foreach ($rolesToPermissions as $roleName => $permissions) {
            if ($role = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first()
            ) {
                $this->syncPermissions($role, $permissions);
            }
        }
    }

    private function applyCustomOrder($permissions, $tenant = null)
    {
        $moduleOrder = $tenant === null ? $this->moduleOrderCentral : $this->moduleOrderTenant;

        // Crear un mapa de módulo a orden
        $orderMap = array_flip($moduleOrder);

        // Ordenar permisos según el orden del módulo
        return $permissions->sort(function ($a, $b) use ($orderMap) {
            $moduleA = $this->getModuleFromPermission($a['name']);
            $moduleB = $this->getModuleFromPermission($b['name']);

            $orderA = $orderMap[$moduleA] ?? 999;
            $orderB = $orderMap[$moduleB] ?? 999;

            return $orderA <=> $orderB;
        })->values();
    }

    private function getModuleFromPermission($permissionName)
    {
        // Extraer el módulo del nombre del permiso
        $parts = explode('.', $permissionName);
        $module = $parts[0];

        // Mapear algunos casos especiales
        $moduleMap = [
            'binnacle' => 'binnacle',
            'emails-sender' => 'emails-sender',
            'email-templates' => 'email-templates',
            'patrolling-reports' => 'patrolling-reports',
            'permit-types' => 'permit-types',
            'banned-vehicles' => 'banned-vehicles',
            'my-requests' => 'my-requests',
            'spots' => 'spots'
        ];

        return $moduleMap[$module] ?? $module;
    }
}
