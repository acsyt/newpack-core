<?php

namespace App\Queries;

use App\Helpers\FilterHelper;
use App\Models\InventoryStock;
use App\Queries\BaseQuery;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class InventoryStockQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return InventoryStock::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('product_id'),
            AllowedFilter::exact('warehouse_id'),
            AllowedFilter::exact('warehouse_location_id'),
            AllowedFilter::exact('batch_id'),
            AllowedFilter::exact('status'),

            AllowedFilter::callback('created_at', FilterHelper::dateRange('created_at')),
            AllowedFilter::callback('updated_at', FilterHelper::dateRange('updated_at')),
            AllowedFilter::callback('search', function ($query, $value) {
                $query->whereHas('product', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%")
                      ->orWhere('sku', 'like', "%{$value}%");
                });
            }),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('quantity'),
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
            'product.productType',
            'warehouse',
            'warehouseLocation',
            'batch',
        ];
    }
}
