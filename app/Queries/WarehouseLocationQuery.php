<?php

namespace App\Queries;

use App\Models\WarehouseLocation;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

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
            AllowedSort::field('id'),
            AllowedSort::field('warehouse_id'),
            AllowedSort::field('aisle'),
            AllowedSort::field('shelf'),
            AllowedSort::field('section'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
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
