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
            ['name' => 'service-customer', 'description' => 'Servicio al cliente'],
            ['name' => 'billing', 'description' => 'Facturación'],
            ['name' => 'inventory', 'description' => 'Almacén'],
            ['name' => 'production', 'description' => 'Producción'],
            ['name' => 'quality', 'description' => 'Calidad'],
        ]);

        $this->allPermissions = collect([
            ['name' => 'dashboard.index',       'description' => 'Ingresar a módulo',           'roles' => ['admin']],

            ['name' => 'users.index',           'description' => 'Listar usuarios',             'roles' => ['admin']],
            ['name' => 'users.create',          'description' => 'Crear usuario',               'roles' => ['admin']],
            ['name' => 'users.export',          'description' => 'Exportar listado de usuarios','roles' => ['admin']],
            ['name' => 'users.show',            'description' => 'Ver detalles de usuario',     'roles' => ['admin']],
            ['name' => 'users.edit',            'description' => 'Editar usuario',              'roles' => ['admin']],
            ['name' => 'users.change-password', 'description' => 'Cambiar contraseña de usuario','roles' => ['admin']],

            ['name' => 'roles.index',           'description' => 'Listar roles',                'roles' => ['admin']],
            ['name' => 'roles.create',          'description' => 'Crear rol',                   'roles' => ['admin']],
            ['name' => 'roles.export',          'description' => 'Exportar listado de roles',   'roles' => ['admin']],
            ['name' => 'roles.show',            'description' => 'Ver detalles de rol',         'roles' => ['admin']],
            ['name' => 'roles.edit',            'description' => 'Editar rol',                  'roles' => ['admin']],

            ['name' => 'classes.index',         'description' => 'Listar clases',               'roles' => ['admin']],
            ['name' => 'classes.create',        'description' => 'Crear clase',                 'roles' => ['admin']],
            ['name' => 'classes.export',        'description' => 'Exportar listado de clases',  'roles' => ['admin']],
            ['name' => 'classes.show',          'description' => 'Ver detalles de clase',       'roles' => ['admin']],
            ['name' => 'classes.edit',          'description' => 'Editar clase',                'roles' => ['admin']],

            ['name' => 'subclasses.index',      'description' => 'Listar subclases',            'roles' => ['admin']],
            ['name' => 'subclasses.create',     'description' => 'Crear subclase',              'roles' => ['admin']],
            ['name' => 'subclasses.export',     'description' => 'Exportar listado de subclases','roles' => ['admin']],
            ['name' => 'subclasses.show',       'description' => 'Ver detalles de subclase',    'roles' => ['admin']],
            ['name' => 'subclasses.edit',       'description' => 'Editar subclase',             'roles' => ['admin']],

            ['name' => 'machines.index',        'description' => 'Listar máquinas',             'roles' => ['admin']],
            ['name' => 'machines.create',       'description' => 'Crear máquina',               'roles' => ['admin']],
            ['name' => 'machines.export',       'description' => 'Exportar listado de máquinas','roles' => ['admin']],
            ['name' => 'machines.show',         'description' => 'Ver detalles de máquina',     'roles' => ['admin']],
            ['name' => 'machines.edit',         'description' => 'Editar máquina',              'roles' => ['admin']],
            ['name' => 'machines.destroy',      'description' => 'Eliminar máquina',            'roles' => ['admin']],

            ['name' => 'processes.index',       'description' => 'Listar procesos',             'roles' => ['admin']],
            ['name' => 'processes.create',      'description' => 'Crear proceso',               'roles' => ['admin']],
            ['name' => 'processes.export',      'description' => 'Exportar listado de procesos','roles' => ['admin']],
            ['name' => 'processes.show',        'description' => 'Ver detalles de proceso',     'roles' => ['admin']],
            ['name' => 'processes.edit',        'description' => 'Editar proceso',              'roles' => ['admin']],

            ['name' => 'raw-materials.index',   'description' => 'Listar materias primas',      'roles' => ['admin']],
            ['name' => 'raw-materials.create',  'description' => 'Crear materia prima',         'roles' => ['admin']],
            ['name' => 'raw-materials.export',  'description' => 'Exportar listado de materias primas','roles' => ['admin']],
            ['name' => 'raw-materials.show',    'description' => 'Ver detalles de materia prima','roles' => ['admin']],
            ['name' => 'raw-materials.edit',    'description' => 'Editar materia prima',        'roles' => ['admin']],

            ['name' => 'suppliers.index',       'description' => 'Listar proveedores',          'roles' => ['admin']],
            ['name' => 'suppliers.create',      'description' => 'Crear proveedor',             'roles' => ['admin']],
            ['name' => 'suppliers.export',      'description' => 'Exportar listado de proveedores','roles' => ['admin']],
            ['name' => 'suppliers.show',        'description' => 'Ver detalles de proveedor',   'roles' => ['admin']],
            ['name' => 'suppliers.edit',        'description' => 'Editar proveedor',            'roles' => ['admin']],

            ['name' => 'customers.index',       'description' => 'Listar clientes',             'roles' => ['admin']],
            ['name' => 'customers.create',      'description' => 'Crear cliente',               'roles' => ['admin']],
            ['name' => 'customers.export',      'description' => 'Exportar listado de clientes','roles' => ['admin']],
            ['name' => 'customers.show',        'description' => 'Ver detalles de cliente',     'roles' => ['admin']],
            ['name' => 'customers.edit',        'description' => 'Editar cliente',              'roles' => ['admin']],

            ['name' => 'warehouses.index',      'description' => 'Listar almacenes',            'roles' => ['admin']],
            ['name' => 'warehouses.create',     'description' => 'Crear almacén',               'roles' => ['admin']],
            ['name' => 'warehouses.export',     'description' => 'Exportar listado de almacenes','roles' => ['admin']],
            ['name' => 'warehouses.show',       'description' => 'Ver detalles de almacén',     'roles' => ['admin']],
            ['name' => 'warehouses.edit',       'description' => 'Editar almacén',              'roles' => ['admin']],

            ['name' => 'warehouse-locations.index',  'description' => 'Listar ubicaciones de almacén',  'roles' => ['admin']],
            ['name' => 'warehouse-locations.create', 'description' => 'Crear ubicación de almacén',     'roles' => ['admin']],
            ['name' => 'warehouse-locations.edit',   'description' => 'Editar ubicación de almacén',    'roles' => ['admin']],
            ['name' => 'warehouse-locations.delete', 'description' => 'Eliminar ubicación de almacén',  'roles' => ['admin']],

        ]);
    }

    public function handle()
    {
        try {
            $this->info('Synchronizing permissions in database...');

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

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            foreach ($this->roles as $role) {
                Role::updateOrCreate(
                    ['name' => $role['name'], 'guard_name' => 'web'],
                    [
                        'description' => $role['description'] ?? '',
                        'active' => $role['active'] ?? true,
                    ]
                );
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
