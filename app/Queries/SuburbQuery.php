<?php

namespace App\Queries;

use App\Models\Suburb;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class SuburbQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Suburb::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('zip_code_id'),
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
            'zipCode.city.state.country',
        ];
    }
}
