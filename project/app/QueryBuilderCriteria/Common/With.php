<?php

namespace App\QueryBuilderCriteria\Common;

use App\QueryBuilderCriteria\Criteria;

class With extends Criteria
{
    /**
     * Arguments for with clause
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
        return $queryBuilder->with(...$this->arguments);
    }
}
