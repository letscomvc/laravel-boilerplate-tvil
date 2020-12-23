<?php

namespace App\Support;

use App\Traits\Newable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;

class PaginationBuilder implements Responsable
{
    use Newable;

    const PER_PAGE_DEFAULT = 20;

    private $defaultOrderBy;
    private $perPage;
    private $queryBuilder;
    private $resource;

    public function __construct()
    {
        $this->perPage = static::PER_PAGE_DEFAULT;
        $this->resource = null;
    }

    /**
     * @param  mixed  $subject
     * @return $this
     */
    public function for($subject): self
    {
        $this->queryBuilder = QueryBuilder::for($subject);
        return $this;
    }

    /**
     * @param  int  $perPage
     * @return $this
     */
    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Add query criteria
     *
     * @param  mixed  $criteria
     * @return $this
     */
    public function criteria($criteria): self
    {
        if (is_iterable($criteria)) {
            foreach ($criteria as $criterion) {
                $criterion->apply($this->queryBuilder);
            }
            return $this;
        }

        $criteria->apply($this->queryBuilder);

        return $this;
    }

    /**
     * @param  \Illuminate\Http\Resources\Json\JsonResource|string  $resource
     * @return $this
     */
    public function resource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function build()
    {
        $paginated = $this->queryBuilder
            ->paginate($this->perPage)
            ->appends(request()->query());

        return ($this->resource)
            ? $this->resource::collection($paginated)
            : JsonResource::collection($paginated);
    }

    /**
     * @param  callable  $callable
     * @return $this
     */
    public function tapQueryBuilder(callable $callable): self
    {
        $callable($this->queryBuilder);
        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->build()
            ->response();
    }
}
