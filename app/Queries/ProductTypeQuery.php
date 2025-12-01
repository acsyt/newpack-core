<?php

namespace App\Queries;

use App\Helpers\FilterHelper;
use App\Models\ProductType;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ProductTypeQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return ProductType::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact("id"),
            AllowedFilter::partial('code'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('slug'),
            AllowedFilter::callback('created_at',FilterHelper::dateRange('created_at')),
            AllowedFilter::callback('updated_at',FilterHelper::dateRange('updated_at')),
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
