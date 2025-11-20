<?php

namespace App\Http\Actions\Shared\User;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UpdateUserAction
{
    public function handle($id, array $data) {

        DB::beginTransaction();
        try {

            $userClass = tenant() ? \App\Models\Tenant\User::class : \App\Models\Central\User::class;
            $user = $userClass::findOrFail($id);

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if (isset($data['role_id'])) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $user->assignRole($role);
                }
            }

            $user->update($data);

            DB::commit();
            return $user->fresh();
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new CustomException('Error updating user', 500);
        }

    }
}
