<?php

namespace App\QueryBuilderCriteria\Common;

use App\QueryBuilderCriteria\Criteria;

class OrderBy extends Criteria
{
    /**
     * Arguments for where clause
     *
     * @var array
     */
    private $arguments;

    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function __construct()
    {
        $this->arguments = func_get_args();
    }

    public function apply($queryBuilder)
    {
        return $queryBuilder->orderBy(...$this->arguments);
    }
}
