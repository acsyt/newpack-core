<?php

namespace App\Queries;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

abstract class BaseQuery
{
    abstract protected function getModel(): string;

    abstract protected function getAllowedFilters(): array;

    abstract protected function getAllowedSorts(): array;

    abstract protected function getDefaultSort(): string;

    abstract protected function getIncludes(): array;

    public function paginated(Request $request, ?Closure $extraQuery = null): Collection | LengthAwarePaginator {
        $hasPagination = $request->boolean('has_pagination', true);
        $perPage = $request->integer('per_page', 10);

        $query = $this->buildQuery($request);

        if ($extraQuery) {
            $extraQuery($query);
        }

        if ($hasPagination) {
            return $query->paginate($perPage);
        } else {
            return $query->get();
        }
    }

    public function get(Request $request, ?Closure $extraQuery = null): Collection {
        $query = $this->buildQuery($request);

        if ($extraQuery) {
            $extraQuery($query);
        }

        if ($request->has('limit')) {
            $limit = $request->integer('limit');
            if ($limit > 0) {
                $query->limit($limit);
            }
        }

        return $query->get();
    }

    public function first(Request $request): ?Model
    {
        return $this->buildQuery($request)->first();
    }

    public function findById(int $id): ?Model
    {
        return $this->getModel()::find($id);
    }

    public function count(Request $request): int
    {
        return $this->buildQuery($request)->count();
    }

    protected function buildQuery(Request $request): QueryBuilder
    {
        $query = QueryBuilder::for($this->getModel())
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->getAllowedSorts())
            ->allowedIncludes($this->getIncludes())
            ->defaultSort($this->getDefaultSort());

        if ($request) {
            $query = $this->applyCustomFilters($query, $request);
        }

        return $query;
    }

    protected function applyCustomFilters(QueryBuilder $query, Request $request): QueryBuilder
    {
        return $query;
    }

    public function getQueryBuilder(Request $request): QueryBuilder
    {
        return $this->buildQuery($request);
    }
}
