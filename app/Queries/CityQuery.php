<?php

namespace App\Queries;

use App\Models\City;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class CityQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return City::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('state_id'),
            AllowedFilter::exact('active'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('name'),
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
            'state.country',
            'zipCodes',
        ];
    }
}
