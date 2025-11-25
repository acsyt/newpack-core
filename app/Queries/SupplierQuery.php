<?php

namespace App\Queries;

use App\Models\Supplier;
use App\Queries\BaseQuery;
use Illuminate\Http\Request;
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
            'company_name',
            'contact_name',
            'email',
            'created_at',
            'updated_at',
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
            'bankAccounts',
        ];
    }
}
