<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    protected $signature = 'sync:permissions';
    protected $description = 'Sincroniza los permisos con la base de datos';

    private $roles;
    private $allPermissions;

    private $moduleOrderCentral = [
        'dashboard',
        'users',
        'roles',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->roles = collect([
            ['name' => 'admin', 'description' => 'Administrator'],
        ]);

        $this->allPermissions = collect([
            ['name' => 'dashboard.index',       'description' => 'Ingresar a módulo',           'roles' => ['admin']],

            ['name' => 'users.index',           'description' => 'Listar usuarios',             'roles' => ['admin']],
            ['name' => 'users.create',          'description' => 'Crear usuario',               'roles' => ['admin']],
            ['name' => 'users.export',          'description' => 'Exportar listado de usuarios','roles' => ['admin']],
            ['name' => 'users.show',            'description' => 'Ver detalles de usuario',     'roles' => ['admin']],
            ['name' => 'users.edit',            'description' => 'Editar usuario',              'roles' => ['admin']],
            ['name' => 'users.change-password', 'description' => 'Cambiar contraseña de usuario','roles' => ['admin']],
        ]);
    }

    public function handle()
    {
        try {
            $this->info('Synchronizing permissions in database...');

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $this->syncDatabase();

            $this->info('Permissions synchronized successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error("Error during synchronization: {$e->getMessage()}");
            return 1;
        }
    }

    private function syncDatabase()
    {
        DB::transaction(function () {
            foreach ($this->roles as $role) {
                Role::updateOrCreate(
                    ['name' => $role['name'], 'guard_name' => 'web'],
                    ['description' => $role['description'] ?? '']
                );
            }

            $configPermissions = $this->allPermissions;
            $configNames = $configPermissions->pluck('name')->toArray();

            $deleted = Permission::where('guard_name', 'web')
                ->whereNotIn('name', $configNames)
                ->delete();

            if ($deleted > 0) {
                $this->warn("Eliminados {$deleted} permisos obsoletos.");
            }

            $orderedPermissions = $this->applyCustomOrder($configPermissions);

            $existingPermissions = Permission::where('guard_name', 'web')
                ->get()
                ->keyBy('name');

            foreach ($orderedPermissions as $index => $configPerm) {
                $permName = $configPerm['name'];
                $permDesc = $configPerm['description'] ?? '';

                $existing = $existingPermissions->get($permName);

                if ($existing) {
                    $hasChanges = false;

                    if ($existing->description !== $permDesc) {
                        $existing->description = $permDesc;
                        $hasChanges = true;
                    }

                    if ($existing->order !== $index) {
                        $existing->order = $index;
                        $hasChanges = true;
                    }

                    if ($hasChanges) {
                        $existing->save();
                        $this->info("Actualizado: {$permName}");
                    }
                } else {
                    Permission::create([
                        'name'        => $permName,
                        'guard_name'  => 'web',
                        'description' => $permDesc,
                        'order'       => $index,
                    ]);
                    $this->info("Creado: {$permName}");
                }
            }

            $this->assignPermissionsToRoles($configPermissions);
        });
    }

    private function assignPermissionsToRoles($filteredPermissions)
    {
        $rolesToPermissions = [];

        foreach ($filteredPermissions as $permission) {
            foreach ($permission['roles'] ?? [] as $roleName) {
                $rolesToPermissions[$roleName][] = $permission['name'];
            }
        }

        foreach ($rolesToPermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role) {
                $role->syncPermissions($permissionNames);
                $count = count($permissionNames);
                $this->line("Rol [{$roleName}] sincronizado con {$count} permisos.");
            }
        }
    }

    private function applyCustomOrder($permissions)
    {
        $moduleOrder = array_flip($this->moduleOrderCentral);

        return $permissions->sort(function ($a, $b) use ($moduleOrder) {
            $moduleA = explode('.', $a['name'])[0];
            $moduleB = explode('.', $b['name'])[0];

            $orderA = $moduleOrder[$moduleA] ?? 999;
            $orderB = $moduleOrder[$moduleB] ?? 999;

            return $orderA <=> $orderB;
        })->values();
    }
}
