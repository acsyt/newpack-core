<?php

namespace App\Queries;

use App\Helpers\FilterHelper;
use App\Models\Transfer;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class TransferQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Transfer::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('source_warehouse_id'),
            AllowedFilter::exact('destination_warehouse_id'),
            AllowedFilter::exact('shipped_by_user_id'),
            AllowedFilter::exact('received_by_user_id'),

            AllowedFilter::partial('search', 'transfer_number'),

            AllowedFilter::callback('shipped_at', FilterHelper::dateRange('shipped_at')),
            AllowedFilter::callback('received_at', FilterHelper::dateRange('received_at')),
            AllowedFilter::callback('created_at', FilterHelper::dateRange('created_at')),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('transfer_number'),
            AllowedSort::field('status'),
            AllowedSort::field('shipped_at'),
            AllowedSort::field('received_at'),
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
            'items',
            'items.product',
            'items.product.measureUnit',
            'items.product.productType',
            'items.sourceLocation',
            'items.destinationLocation',
            'items.batch',
            'sourceWarehouse',
            'destinationWarehouse',
            'shippedByUser',
            'receivedByUser',
            'inventoryMovements',
            'inventoryMovements.product',
        ];
    }
}
