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
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by role name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[description]",
     *         in="query",
     *         description="Filter by description (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[active]",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
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
    public function findAll()
    {
        $query = $this->roleQuery->paginated(request());
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
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function show(Role $role)
    {
        return new RoleResource($role);
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
    public function store(SaveRoleRequest $request)
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
    public function update(SaveRoleRequest $request, Role $role)
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

