<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\CreateUserAction;
use App\Http\Actions\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Queries\UserQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: 'Users')]
class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List all users",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by user ID (exact match)"),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by name (partial match)"),
     *     @OA\Parameter(name="filter[username]", in="query", required=false, @OA\Schema(type="string"), description="Filter by username (partial match)"),
     *     @OA\Parameter(name="filter[email]", in="query", required=false, @OA\Schema(type="string"), description="Filter by email (partial match)"),
     *     @OA\Parameter(name="filter[last_name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by last name (partial match)"),
     *     @OA\Parameter(name="filter[phone]", in="query", required=false, @OA\Schema(type="string"), description="Filter by phone (partial match)"),
     *     @OA\Parameter(name="filter[active]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by active status (exact match)"),
     *     @OA\Parameter(name="filter[role_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by role ID (exact match)"),
     *     @OA\Parameter(name="filter[created_at]", in="query", required=false, @OA\Schema(type="string"), description="Filter by creation date range (format: YYYY-MM-DD,YYYY-MM-DD)"),
     *     @OA\Parameter(name="filter[updated_at]", in="query", required=false, @OA\Schema(type="string"), description="Filter by last update date range (format: YYYY-MM-DD,YYYY-MM-DD)"),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     )
     * )
     */
    public function findAllUsers()
    {
        $users = UserQuery::make()->paginated();
        return UserResource::collection($users);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user by ID",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function findOneUser($id)
    {
        $user = UserQuery::make()->findByIdOrFail( $id );
        return new UserResource($user);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="message", type="string", example="User created successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createUser(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = CreateUserAction::handle($data);
        return response()->json([
            'data'      => new UserResource($user),
            'message'   => 'User created successfully'
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="message", type="string", example="User updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateUser(UpdateUserRequest $request, $id): Response
    {
        /** @var User $user */
        $user = UserQuery::make()->findByIdOrFail($id);
        $data = $request->validated();
        $updatedUser = UpdateUserAction::handle($user, $data);
        return response()->json([
            'data'    => new UserResource($updatedUser),
            'message' => 'User updated successfully'
        ]);
    }

}
