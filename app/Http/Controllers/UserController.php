<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\CreateUserAction;
use App\Http\Actions\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Queries\UserQuery;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function __construct(
        private readonly UserQuery $userQuery,
    ) {
    }


    public function findAll()
    {
        $query = $this->userQuery->paginated(request());
        return UserResource::collection($query);
    }

    public function show($id)
    {
        $query = $this->userQuery->findById($id);
        return new UserResource($query);
    }


    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = app(CreateUserAction::class)->handle($data);
        return response()->json([
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    public function update(UpdateUserRequest $request, $id): Response
    {
        $data = $request->validated();
        $updatedUser = app(UpdateUserAction::class)->handle($id, $data);

        return response()->json([
            'data' => $updatedUser,
            'message' => 'User updated successfully'
        ]);
    }

}
