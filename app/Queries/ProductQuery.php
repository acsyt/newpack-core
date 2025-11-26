<?php

namespace App\Queries;

use App\Models\Product;
use Spatie\QueryBuilder\AllowedFilter;

class ProductQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Product::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::partial('sku'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::exact('is_sellable'),
            AllowedFilter::exact('is_purchasable'),
            AllowedFilter::scope('raw_material'),
            AllowedFilter::scope('compound'),
            AllowedFilter::scope('search'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            'name',
            'sku',
            'type',
            'current_stock',
            'average_cost',
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
            'ingredients',
            'usedInCompounds',
        ];
    }
}
