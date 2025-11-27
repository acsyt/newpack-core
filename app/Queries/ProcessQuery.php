<?php

namespace App\Queries;

use App\Models\Process;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ProcessQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Process::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('code'),
            AllowedFilter::partial('name'),
            AllowedFilter::exact('applies_to_pt'),
            AllowedFilter::exact('applies_to_mp'),
            AllowedFilter::exact('applies_to_compounds'),
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
