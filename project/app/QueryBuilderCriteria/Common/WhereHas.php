<?php

namespace App\QueryBuilderCriteria\Common;

use App\QueryBuilderCriteria\Criteria;

class WhereHas extends Criteria
{
    /**
     * Arguments for where clause
     *
     * @var array
     */
    private $arguments;

    public function __construct()
    {
        $this->arguments = func_get_args();
    }

    public function apply($queryBuilder)
    {
        return $queryBuilder->whereHas(...$this->arguments);
    }
}
