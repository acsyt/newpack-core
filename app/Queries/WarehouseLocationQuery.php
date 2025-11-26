<?php

namespace App\Queries;

use App\Models\WarehouseLocation;
use Spatie\QueryBuilder\AllowedFilter;

class WarehouseLocationQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return WarehouseLocation::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('warehouse_id'),
            AllowedFilter::partial('aisle'),
            AllowedFilter::partial('shelf'),
            AllowedFilter::partial('section'),
            AllowedFilter::exact('unique_id'),
            AllowedFilter::scope('search'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            'warehouse_id',
            'aisle',
            'shelf',
            'section',
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
            'warehouse',
        ];
    }
}
