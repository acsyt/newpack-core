<?php

namespace App\Queries;

use App\Helpers\FilterHelper;
use App\Models\User;
use App\Queries\BaseQuery;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class UserQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return User::class;
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
            AllowedSort::field('id'),
            AllowedSort::field('name'),
            AllowedSort::field('email'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];
    }

    protected function getDefaultSort(): string
    {
        return 'created_at';
    }

    protected function getAllowedIncludes(): array
    {
        return [
            'roles',
            'permissions',
        ];
    }
}
