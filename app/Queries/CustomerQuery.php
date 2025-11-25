<?php

namespace App\Queries;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class CustomerQuery
{
    protected QueryBuilder $query;

    public function __construct()
    {
        $this->query = QueryBuilder::for(Customer::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('last_name'),
                AllowedFilter::partial('email'),
                AllowedFilter::partial('rfc'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('client_type'),
                AllowedFilter::exact('suburb_id'),
                AllowedFilter::scope('search'),
            ])
            ->allowedIncludes(['suburb.zipCode.city.state'])
            ->allowedSorts(['name', 'last_name', 'email', 'created_at', 'updated_at'])
            ->defaultSort('-created_at');
    }

    public function paginated(Request $request, int $perPage = 25)
    {
        $perPage = min((int) $request->input('per_page', 25), 100);
        return $this->query->paginate($perPage);
    }

    public function findById(int $id): ?Customer
    {
        return Customer::with(['suburb.zipCode.city.state', 'createdBy', 'updatedBy'])->find($id);
    }
}
