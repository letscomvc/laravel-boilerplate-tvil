<?php

namespace App\QueryBuilderCriteria\Common;

use App\QueryBuilderCriteria\Criteria;

class SearchResolvedByUrlQuery extends Criteria
{
    /** @var array|null */
    private $searchBy;

    /** @var array|null */
    private $searchByRelationship;

    public function __construct(
        ?array $searchBy = null,
        ?array $searchByRelationship = null
    ) {
        $this->searchBy = $searchBy;
        $this->searchByRelationship = $searchByRelationship;
    }

    public function apply($queryBuilder)
    {
        if (!$query = request('query')) {
            return $queryBuilder;
        }

        return tap($queryBuilder)
            ->search(
                $query,
                $this->searchBy,
                $this->searchByRelationship
            );
    }
}
