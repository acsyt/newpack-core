<?php

namespace App\Http\Controllers;

use App\Http\Actions\Role\CreateRoleAction;
use App\Http\Actions\Role\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\SaveRoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Queries\RoleQuery;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Permission;

#[OA\Tag(name: 'Roles')]
class RoleController extends Controller
{
    public function __construct(
        private readonly RoleQuery $roleQuery,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="List all roles",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by role ID (exact match)"),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by role name (partial match)"),
     *     @OA\Parameter(name="filter[description]", in="query", required=false, @OA\Schema(type="string"), description="Filter by description (partial match)"),
     *     @OA\Parameter(name="filter[guard_name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by guard name (exact match)"),
     *     @OA\Parameter(name="filter[active]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by active status (exact match)"),
     *     @OA\Parameter(name="filter[created_at]", in="query", required=false, @OA\Schema(type="string"), description="Filter by creation date range (format: YYYY-MM-DD,YYYY-MM-DD)"),
     *     @OA\Parameter(name="filter[updated_at]", in="query", required=false, @OA\Schema(type="string"), description="Filter by last update date range (format: YYYY-MM-DD,YYYY-MM-DD)"),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by field (prefix with - for descending)",
     *         required=false,
     *         @OA\Schema(type="string", example="name")
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related resources (permissions)",
     *         required=false,
     *         @OA\Schema(type="string", example="permissions")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RoleResource")
     *         )
     *     )
     * )
     */
    public function findAllRoles()
    {
        $query = $this->roleQuery->paginated();
        return RoleResource::collection($query);
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Get role by ID",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related resources (permissions)",
     *         required=false,
     *         @OA\Schema(type="string", example="permissions")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/RoleResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function findOneRole(Role $role)
    {
        return response()->json(new RoleResource($role));
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Create a new role",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SaveRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Role created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/RoleResource"),
     *             @OA\Property(property="message", type="string", example="Role created successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createRole(SaveRoleRequest $request)
    {
        $data = $request->validated();
        $role = app(CreateRoleAction::class)->handle($data);
        return response()->json([
            'data'      => new RoleResource($role),
            'message'   => 'Role created successfully'
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Update a role",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SaveRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/RoleResource"),
     *             @OA\Property(property="message", type="string", example="Role updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Role not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateRole(SaveRoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $updatedRole = app(UpdateRoleAction::class)->handle($role, $data);

        return response()->json([
            'data'    => new RoleResource($updatedRole),
            'message' => 'Role updated successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     summary="List all available permissions",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of all available permissions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PermissionResource")
     *         )
     *     )
     * )
     */
    public function getPermissions()
    {
        $permissions = Permission::orderBy('order', 'asc')->get();
        return PermissionResource::collection($permissions);
    }
}

