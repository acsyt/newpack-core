<?php

namespace App\Queries;

use App\Models\Country;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class CountryQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Country::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('code'),
            AllowedFilter::exact('active'),
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
        return [
            'states',
        ];
    }
}
