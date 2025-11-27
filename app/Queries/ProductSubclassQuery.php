<?php

namespace App\Queries;

use App\Models\ProductSubclass;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\AllowedInclude;

class ProductSubclassQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return ProductSubclass::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('code'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
            AllowedFilter::exact('product_class_id'),
            AllowedFilter::scope('search'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('code'),
            AllowedSort::field('name'),
            AllowedSort::field('product_class_id'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];
    }

    protected function getAllowedIncludes(): array
    {
        return [
            AllowedInclude::relationship('productClass'),
        ];
    }

    protected function getDefaultSort(): string
    {
        return 'name';
    }
}
