<?php

namespace App\Http\Actions\User;

use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordAction
{
    public static function handle(User $user, string $password)
    {
        DB::beginTransaction();
        try {
            $user->password = Hash::make($password);
            $user->save();

            DB::commit();
            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new CustomException('Error updating user password', 500);
        }
    }
}
