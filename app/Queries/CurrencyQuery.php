<?php

namespace App\Queries;

use App\Helpers\FilterHelper;
use App\Models\Currency;
use App\Queries\BaseQuery;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class CurrencyQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Currency::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('code'),
            AllowedFilter::exact('active'),

            AllowedFilter::callback('created_at', FilterHelper::dateRange('created_at')),
            AllowedFilter::callback('updated_at', FilterHelper::dateRange('updated_at')),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('name'),
            AllowedSort::field('code'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];
    }

    protected function getDefaultSort(): string
    {
        return 'name';
    }

    protected function getAllowedIncludes(): array
    {
        return [];
    }
}
