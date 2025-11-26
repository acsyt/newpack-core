<?php

namespace App\Queries;

use App\Models\Warehouse;
use Spatie\QueryBuilder\AllowedFilter;

class WarehouseQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Warehouse::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('active'),
            AllowedFilter::scope('search'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            'name',
            'type',
            'active',
            'created_at',
            'updated_at',
        ];
    }

    protected function getDefaultSort(): string
    {
        return '-created_at';
    }

    protected function getAllowedIncludes(): array
    {
        return [
            'warehouseLocations',
        ];
    }
}
