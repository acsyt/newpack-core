<?php

namespace App\Queries;

use App\Models\Product;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

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
            AllowedSort::field('id'),
            AllowedSort::field('name'),
            AllowedSort::field('sku'),
            AllowedSort::field('type'),
            AllowedSort::field('current_stock'),
            AllowedSort::field('average_cost'),
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
            'ingredients',
            'usedInCompounds',
        ];
    }
}
