<?php

namespace App\Queries;

use App\Helpers\FilterHelper;
use App\Models\InventoryMovement;
use App\Queries\BaseQuery;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class InventoryMovementQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return InventoryMovement::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('product_id'),
            AllowedFilter::exact('warehouse_id'),
            AllowedFilter::exact('warehouse_location_id'),
            AllowedFilter::exact('batch_id'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('user_id'),
            AllowedFilter::exact('reference_type'),
            AllowedFilter::exact('reference_id'),

            AllowedFilter::callback('created_at', FilterHelper::dateRange('created_at')),
            AllowedFilter::callback('updated_at', FilterHelper::dateRange('updated_at')),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('quantity'),
            AllowedSort::field('balance_after'),
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
            'product',
            'product.measureUnit',
            'warehouse',
            'warehouseLocation',
            'batch',
            'user',
            'reference',
            'relatedMovement',
            'relatedMovement.warehouse',
        ];
    }
}
