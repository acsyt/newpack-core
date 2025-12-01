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
            AllowedFilter::exact('status'),

            // Product filters
            AllowedFilter::callback('product.sku', function ($query, $value) {
                $query->whereHas('product', function ($q) use ($value) {
                    $q->where('sku', 'like', "%{$value}%");
                });
            }),
            AllowedFilter::callback('product.name', function ($query, $value) {
                $query->whereHas('product', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            }),
            AllowedFilter::callback('product.product_type_id', function ($query, $value) {
                $query->whereHas('product', function ($q) use ($value) {
                    $q->where('product_type_id', $value);
                });
            }),

            // Warehouse filters
            AllowedFilter::exact('warehouse.id', 'warehouse_id'),

            // Warehouse Location filters
            AllowedFilter::callback('warehouseLocation.search', function ($query, $value) {
                $query->whereHas('warehouseLocation', function ($q) use ($value) {
                    $q->whereRaw("CONCAT(aisle, ' - ', shelf, ' - ', section) LIKE ?", ["%{$value}%"]);
                });
            }),

            // Batch filters
            AllowedFilter::callback('batch.code', function ($query, $value) {
                $query->whereHas('batch', function ($q) use ($value) {
                    $q->where('code', 'like', "%{$value}%");
                });
            }),

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
