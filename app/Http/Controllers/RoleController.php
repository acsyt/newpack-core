<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\SaveRoleRequest;
use App\Http\Resources\Shared\PermissionResource;
use App\Http\Resources\Shared\RoleResource;
use App\Models\Shared\Role;
use App\Models\Tenant;
use App\Services\Shared\RoleService;
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
            'message'   => __('app.common.feedback.success.created.singular.masculine', [
                'entity' => __('app.common.entities.role.singular')
            ]),
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
            'message'   => __('app.common.feedback.success.updated.singular.masculine', [
                'entity' => __('app.common.entities.role.singular')
            ]),
            'data'      => new RoleResource( $role )
        ]);
    }

    public function getPermissions() {
        $permissions = Permission::orderBy('order', 'asc')->get();
        return PermissionResource::collection( $permissions );
    }

}
