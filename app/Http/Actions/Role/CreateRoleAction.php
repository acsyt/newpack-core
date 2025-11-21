<?php

namespace App\Http\Actions\Role;

use App\Exceptions\CustomException;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateRoleAction
{
    public function handle(array $data)
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name'          => Str::slug($data['name']),
                'description'   => $data['description'] ?? Str::title($data['name']),
                'guard_name'    => $data['guard_name'] ?? 'web',
                'active'        => $data['active'] ?? true,
            ]);

            if (isset($data['permissions']) && !empty($data['permissions'])) {
                $this->syncPermissions($role, $data['permissions']);
            }

            DB::commit();

            return $role->load('permissions');
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            throw new CustomException('Error creating role', 500);
        }
    }

    private function syncPermissions(Role $role, array $newPermissions): void
    {
        $permissions = Permission::whereIn('name', $newPermissions)->get();

        $permissionNames = $permissions->pluck('name')->toArray();
        $missingPermissions = array_diff($newPermissions, $permissionNames);

        if (!empty($missingPermissions)) {
            throw new CustomException("The following permissions do not exist: " . implode(', ', $missingPermissions));
        }

        $role->syncPermissions($permissions);
    }
}
