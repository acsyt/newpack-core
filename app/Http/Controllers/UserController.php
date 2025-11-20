<?php

namespace App\Http\Controllers\Shared;

use App\Http\Actions\Shared\User\CreateUserAction;
use App\Http\Actions\Shared\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\PasswordRequest;
use App\Http\Requests\Shared\User\StoreUserRequest;
use App\Http\Requests\Shared\User\UpdateUserRequest;
use App\Http\Requests\Shared\UserRequest;
use App\Http\Resources\Shared\UserResource;
use App\Queries\Shared\UserQuery;
use App\Services\Shared\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function __construct(
        private readonly UserQuery $userQuery,
    ) {}


    public function findAll()
    {
        $query = $this->userQuery->paginated(request());
        return UserResource::collection( $query );
    }

    public function show($id)
    {
        $query = $this->userQuery->findById($id);
        return new UserResource($query);
    }


    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = app(CreateUserAction::class)->handle( $data );
        return response()->json([
            'data'      => $user,
            'message'   => 'User created successfully'
        ], 201);
    }

    public function update(UpdateUserRequest $request, $id): Response
    {
        $data = $request->validated();
        $updatedUser = app(UpdateUserAction::class)->handle( $id, $data );

        return response()->json([
            'data'      => $updatedUser,
            'message'   => 'User updated successfully'
        ]);
    }

}
