<?php

namespace GrupoCometa\Includes\Query;

use GrupoCometa\Builder\QueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class IncludesRelationship
{
    private $with;
    public function __construct(private Builder $builder, private Request $request)
    {
        $function = gettype($this->request->includes) . 'BuildWith';
        $this->$function();
        $this->with();
    }

    private function with()
    {
        if (!$this->with) return;
        $this->builder = $this->builder->with($this->with);
    }

    private function  stringBuildWith()
    {
        $relationships = explode(',', $this->request->includes);
        foreach ($relationships as $relation) {

            $this->with[$relation] = fn ($query) => $query->orderBy($this->builder->getModel()->getKeyOrderBy());
            $this->builder = $this->builder->whereHas($relation, $this->with[$relation]);
        }
    }

    private function arrayBuildWith()
    {
        foreach ($this->request->includes as $relation => $paramns) {

            if (gettype($relation) == 'integer') $relation = $paramns;
            $relations = explode(",", $relation);
            $this->queryRelation($relations, $paramns);
        }
    }

    private function queryRelation($relations, $paramns)
    {
        foreach ($relations as  $relation) {

            $this->with[$relation] =  function ($query) use ($paramns) {
                if (gettype($paramns) == 'string') return $query->where($this->builder->getPrimaryKey(), '<>', null);
                (new QueryString($query, $paramns))->getBuilder();
            };

            $this->builder = $this->builder->whereHas($relation, $this->with[$relation]);
        }
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
