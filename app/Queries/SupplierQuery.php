<?php

namespace App\Queries;

use App\Models\Supplier;
use App\Queries\BaseQuery;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class SupplierQuery extends BaseQuery
{
    protected function getModel(): string
    {
        return Supplier::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('company_name'),
            AllowedFilter::partial('contact_name'),
            AllowedFilter::partial('email'),
            AllowedFilter::partial('rfc'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('supplier_type'),
            AllowedFilter::exact('suburb_id'),
            AllowedFilter::scope('search'),
        ];
    }

    protected function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('id'),
            AllowedSort::field('company_name'),
            AllowedSort::field('contact_name'),
            AllowedSort::field('email'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];
    }

    protected function getDefaultSort(): string
    {
        return '-created_at';
    }

    protected function getAllowedIncludes(): array
    {
        return [
            'suburb.zipCode.city.state',
        ];
    }
}
