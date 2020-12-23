<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait SearchScope
{
    public function scopeSearch(
        $query,
        string $term,
        ?array $searchBy = null,
        ?array $searchByRelationship = null
    ) {
        if (method_exists(get_called_class(), 'setupSearch')) {
            $this->setupSearch();
        }

        if ($searchBy !== null) {
            $this->searchBy = array_merge_recursive(
                Arr::wrap($this->searchBy),
                $searchBy
            );
        }

        if ($searchByRelationship !== null) {
            $this->searchByRelationship = array_merge_recursive(
                $this->searchByRelationship,
                $searchByRelationship
            );
        }

        $query->where(function ($query) use ($term) {
            if (isset($this->searchBy) && is_array($this->searchBy)) {
                $this->addConditions($query, $term, $this->searchBy);
            }

            if (isset($this->searchByRelationship) && is_array($this->searchByRelationship)) {
                $this->addConditionsForRelationships($query, $term, $this->searchByRelationship);
            }
        });

        return $query;
    }

    private function addConditions($query, $term, $searchBy)
    {
        $searchBy = array_filter($searchBy);
        foreach ($searchBy as $key => $field) {
            $query->orWhereRaw("CAST({$field} as VARCHAR) ILIKE ?", "%$term%");
        }

        return $query;
    }

    private function addConditionsForRelationships($query, $term, $relationships)
    {
        $relationships = array_filter($relationships);
        foreach ($relationships as $relation => $fields) {
            $query->orWhereHas($relation, function ($query) use ($fields, $term) {
                $query->where(function ($query) use ($fields, $term) {
                    $this->addConditions($query, $term, $fields);
                });
            });
        }

        return $query;
    }
}
