<?php

namespace App\Http\Actions\Shared\User;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateUserAction
{
    public function handle(array $data) {
        DB::beginTransaction();
        try {
            $userClass = tenant() ? \App\Models\Tenant\User::class : \App\Models\Central\User::class;

            $newPassword = Str::random(10);
            $data['password'] = Hash::make( $newPassword );

            $user = $userClass::create($data);

            if (isset($data['role_id'])) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $user->assignRole($role);
                }
            }

            DB::commit();

            return $user;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            throw new CustomException('Error creating user', 500);
        }
    }
}
