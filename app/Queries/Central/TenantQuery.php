<?php

namespace App\Queries\Central;

use App\Models\Tenant;
use App\Queries\BaseQuery;
use Spatie\QueryBuilder\AllowedFilter;

class TenantQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Tenant::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('name'),
            AllowedFilter::exact('email')
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
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
        return [];
    }
}
