<?php

namespace App\Queries;

use App\Helpers\FilterHelper;
use App\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;

class RoleQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Role::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
            AllowedFilter::exact('guard_name'),
            AllowedFilter::exact('active'),
            AllowedFilter::callback('created_at', FilterHelper::dateRange('created_at')),
            AllowedFilter::callback('updated_at', FilterHelper::dateRange('updated_at')),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            'id',
            'name',
            'guard_name',
            'created_at',
            'updated_at',
        ];
    }

    protected function getDefaultSort(): string
    {
        return 'name';
    }

    protected function getAllowedIncludes(): array
    {
        return [
            'permissions'
        ];
    }
}
