<?php

namespace App\Queries\Shared;

use App\Helpers\FilterHelper;
use App\Queries\BaseQuery;
use Spatie\QueryBuilder\AllowedFilter;

class UserQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return tenant() ? \App\Models\Tenant\User::class : \App\Models\Central\User::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('username'),
            AllowedFilter::partial('email'),
            AllowedFilter::partial('last_name'),
            AllowedFilter::partial('phone'),
            AllowedFilter::exact('active'),
            AllowedFilter::exact('role_id'),

            AllowedFilter::callback('created_at', FilterHelper::dateRange('created_at')),
            AllowedFilter::callback('updated_at', FilterHelper::dateRange('updated_at')),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ];
    }

    protected function getDefaultSort(): string
    {
        return 'created_at';
    }

    protected function getIncludes(): array
    {
        return [
            'roles',
            'permissions',
        ];
    }
}
