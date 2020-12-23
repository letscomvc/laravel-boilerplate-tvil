<?php

namespace App\QueryBuilderCriteria;

use App\Traits\Newable;

abstract class Criteria
{
    use Newable;

    /**
     * @param $queryBuilder
     * @return mixed
     */
    abstract public function apply($queryBuilder);
}
