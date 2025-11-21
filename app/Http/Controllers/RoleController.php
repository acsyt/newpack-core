<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\SaveRoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;


class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    public function findAll()
    {
        $query = $this->roleService->findAllQuery();
        return $this->query($query, RoleResource::class);
    }


    public function findById(int $id ) {
        $role = $this->roleService->findAllQuery()
            ->findOrFail( $id );
        return response()->json( new RoleResource( $role ) );
    }


    public function createRole( SaveRoleRequest $request ) {
        $data = $request->validated();

        $newRole = $this->roleService->createRole( $data );

        $role = $this->roleService->findAllQuery()->findOrFail( $newRole->id );
        $role->load('permissions');

        return response()->json([
            'message'   => 'Role created successfully',
            'data'      => new RoleResource( $role )
        ]);
    }

    public function updateRole($id, SaveRoleRequest $request ) {
        $data = $request->validated();

        /** @var Role $role */
        $role = Role::findOrFail($id);

        $this->roleService->updateRole( $role, $data );

        $role->load('permissions');

        return response()->json([
            'message'   => 'Role updated successfully',
            'data'      => new RoleResource( $role )
        ]);
    }

    public function getPermissions() {
        $permissions = Permission::orderBy('order', 'asc')->get();
        return PermissionResource::collection( $permissions );
    }

}
