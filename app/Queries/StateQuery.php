<?php

namespace App\Queries;

use App\Models\State;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class StateQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return State::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('code'),
            AllowedFilter::exact('country_id'),
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
            'country',
            'cities',
        ];
    }
}
