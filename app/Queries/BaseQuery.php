<?php

namespace App\Queries;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseQuery
{
    protected $request;
    protected array $withCountRelations = [];

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? request();
    }

    public static function make(?Request $request = null): static
    {
        return new static($request);
    }

    abstract protected function getModel(): string;

    abstract protected function getAllowedFilters(): array;

    abstract protected function getAllowedSorts(): array;

    abstract protected function getAllowedIncludes(): array;

    protected function getAllowedFields(): array
    {
        return [];
    }

    protected function getAllowedAppends(): array
    {
        return [];
    }

    protected function getDefaultSort(): string
    {
        return '-created_at';
    }

    public function withCount(string|array $relations): static
    {
        $this->withCountRelations = array_merge(
            $this->withCountRelations,
            is_array($relations) ? $relations : [$relations]
        );
        return $this;
    }

    public function paginated(?Closure $extraQuery = null): LengthAwarePaginator|Collection
    {
        $hasPaginationValue = $this->request->input('has_pagination', true);
        $hasPagination = filter_var($hasPaginationValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;

        $perPage = $this->request->integer('per_page', 10);

        $query = $this->buildQuery();

        if ($extraQuery) {
            $extraQuery($query);
        }

        if ($hasPagination) {
            return $query->paginate($perPage)->appends($this->request->query());
        }

        return $query->get();
    }

    public function get(?Closure $extraQuery = null): Collection
    {
        $query = $this->buildQuery();

        if ($extraQuery) {
            $extraQuery($query);
        }

        if ($this->request->has('limit')) {
            $limit = $this->request->integer('limit');
            if ($limit > 0) {
                $query->limit($limit);
            }
        }

        return $query->get();
    }


    public function first(): ?Model
    {
        return $this->buildQuery()->first();
    }

    public function findById(int|string $id): ?Model
    {
        return $this->buildQuery()->find($id);
    }

    public function count(): int
    {
        return $this->buildQuery()->count();
    }

    public function getBuilder(): QueryBuilder
    {
        return $this->buildQuery();
    }

    protected function buildQuery(): QueryBuilder
    {
        $query = QueryBuilder::for($this->getModel(), $this->request)
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->getAllowedSorts())
            ->allowedIncludes($this->getAllowedIncludes())
            ->defaultSort($this->getDefaultSort());

        if (!empty($this->withCountRelations)) {
            $query->withCount($this->withCountRelations);
        }

        return $this->applyCustomLogic($query);
    }

    protected function applyCustomLogic(QueryBuilder $query): QueryBuilder
    {
        return $query;
    }

    public function findByIdOrFail(int|string $id): Model
    {
        return $this->buildQuery()->findOrFail($id);
    }
}
