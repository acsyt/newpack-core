<?php

namespace App\Services\Shared;

use App\Exceptions\CustomException;
use App\Helpers\FilterHelper;
use App\Models\Shared\Permission;
use App\Models\Shared\Role;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;


class RoleService
{
    private array $allowedFilters;
    private array $allowedSorts;
    private array $allowedIncludes;

    public function __construct()
    {
        $this->initializeAllowedFilters();
        $this->initializeAllowedSorts();
        $this->initializeAllowedIncludes();
    }

    private function initializeAllowedFilters(): void
    {
        $this->allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
            AllowedFilter::exact('guard_name'),
            AllowedFilter::exact('active'),
            AllowedFilter::callback('created_at', FilterHelper::dateRange('created_at')),
            AllowedFilter::callback('updated_at', FilterHelper::dateRange('updated_at')),
            AllowedFilter::callback('permissions.name', function ($query, $value) {
                $like = "%{$value}%";
                $query->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
                    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                    ->where('permissions.name', 'LIKE',  $like);
            }),

            AllowedFilter::callback('term', function ($query, $value) {
                $query->where(function ($query) use ($value) {
                    $like = "%{$value}%";
                    $query->where('id', 'LIKE', $like)
                        ->orWhere('name', 'LIKE', $like)
                        ->orWhere('description', 'LIKE', $like)
                        ->orWhereDate('created_at', 'LIKE', $like)
                        ->orWhereTime('created_at', 'LIKE', $like)
                        ->orWhereDate('updated_at', 'LIKE', $like)
                        ->orWhereTime('updated_at', 'LIKE', $like);
                });
            }),
        ];
    }

    private function initializeAllowedSorts(): void
    {
        $this->allowedSorts = [
            'id',
            'name',
            'description',
            'guard_name',
            'active',
            'created_at',
            'updated_at',


            AllowedSort::callback('permissions.name', function ($query, $descending) {
                $query->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
                    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                    ->orderBy('permissions.name', $descending ? 'desc' : 'asc');
            }),
        ];
    }

    private function initializeAllowedIncludes(): void
    {
        $this->allowedIncludes = [
            'permissions'
        ];
    }

    public function findAllQuery(): QueryBuilder
    {
        return QueryBuilder::for(Role::class)
            ->allowedFilters($this->allowedFilters)
            ->allowedSorts($this->allowedSorts)
            ->allowedIncludes($this->allowedIncludes);
    }

    public function createRole($data): Role
    {
        DB::beginTransaction();
        try {

            $role = Role::create([
                'name'          => Str::slug($data['name']),
                'description'   => Str::title($data['name']),
                'guard_name'    => 'web',
                'active'        => $data['active'] ?? true,
            ]);

            $this->syncPermissions($role, $data['permissions']);

            DB::commit();
            return $role;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (ValidationException $e) {
            DB::rollback();
            throw $e;
        } catch (QueryException $dbe) {
            DB::rollback();
            Log::error('Error en la base de datos: ' . $dbe->getMessage());
            throw new CustomException('Error al procesar la solicitud. Por favor, inténtalo de nuevo.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error desconocido: ' . $e->getMessage());
            throw new CustomException('Se produjo un error inesperado. Por favor, inténtalo de nuevo.');
        }
    }


    public function updateRole(Role $role, $data): Role
    {
        DB::beginTransaction();
        try {

            $role->update([
                'description'   =>  Str::title($data['name']),
                'active'        => $data['active'] ?? true,
            ]);

            $this->syncPermissions($role, $data['permissions']);

            DB::commit();
            return $role;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (ValidationException $e) {
            DB::rollback();
            throw $e;
        } catch (QueryException $dbe) {
            DB::rollback();
            Log::error('Error en la base de datos: ' . $dbe->getMessage());
            throw new CustomException('Error al procesar la solicitud. Por favor, inténtalo de nuevo.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error desconocido: ' . $e->getMessage());
            throw new CustomException('Se produjo un error inesperado. Por favor, inténtalo de nuevo.');
        }
    }

    private function syncPermissions(Role $role, array $newPermissions): void
    {
        if (empty($newPermissions)) return;

        $permissions = Permission::whereIn('name', $newPermissions)
            ->get();

        $permissionNames = $permissions->pluck('name')->toArray();
        $missingPermissions = array_diff($newPermissions, $permissionNames);

        if (!empty($missingPermissions)) throw new CustomException("Los siguientes permisos no existen: " . implode(', ', $missingPermissions));

        $role->syncPermissions($permissions);
    }
}
