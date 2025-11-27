<?php

namespace App\Queries;

use App\Models\Machine;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\AllowedInclude;

class MachineQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Machine::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('code'),
            AllowedFilter::partial('name'),
            AllowedFilter::exact('process_id'),
            AllowedFilter::exact('status'),
            AllowedFilter::scope('search'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('code'),
            AllowedSort::field('name'),
            AllowedSort::field('process_id'),
            AllowedSort::field('status'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];
    }

    protected function getAllowedIncludes(): array
    {
        return [
            AllowedInclude::relationship('process'),
        ];
    }

    protected function getDefaultSort(): string
    {
        return 'name';
    }
}
