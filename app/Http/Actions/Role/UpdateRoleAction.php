<?php

namespace App\Http\Actions\Role;

use App\Exceptions\CustomException;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateRoleAction
{
    public function handle(Role $role, array $data)
    {
        DB::beginTransaction();
        try {
            $role->update([
                'description'   => $data['description'] ?? Str::title($data['name'] ?? $role->name),
                'active'        => $data['active'] ?? $role->active,
            ]);

            if (isset($data['permissions'])) {
                $this->syncPermissions($role, $data['permissions']);
            }

            DB::commit();
            return $role->fresh()->load('permissions');
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            throw new CustomException('Error updating role', 500);
        }
    }

    private function syncPermissions(Role $role, array $newPermissions): void
    {
        if (empty($newPermissions)) {
            return;
        }

        $permissions = Permission::whereIn('name', $newPermissions)->get();

        $permissionNames = $permissions->pluck('name')->toArray();
        $missingPermissions = array_diff($newPermissions, $permissionNames);

        if (!empty($missingPermissions)) {
            throw new CustomException("The following permissions do not exist: " . implode(', ', $missingPermissions));
        }

        $role->syncPermissions($permissions);
    }
}
