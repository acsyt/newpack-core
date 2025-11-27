<?php

namespace App\Queries;

use App\Models\ProductClass;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ProductClassQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return ProductClass::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('code'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
            AllowedFilter::scope('search'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('code'),
            AllowedSort::field('name'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];
    }

    protected function getAllowedIncludes(): array
    {
        return [];
    }

    protected function getDefaultSort(): string
    {
        return 'name';
    }
}
