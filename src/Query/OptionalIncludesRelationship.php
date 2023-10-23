<?php

namespace GrupoCometa\Includes\Query;

use GrupoCometa\Builder\QueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OptionalIncludesRelationship
{
    private $with;
    public function __construct(private Builder|HasMany|BelongsTo|HasOne|BelongsToMany  $builder, private Request $request)
    {
        $function = gettype($this->request->optionalIncludes) . 'BuildWith';
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
        $relationships = explode(',', $this->request->optionalIncludes);
        foreach ($relationships as $relation) {
            $orderBy = $this->getOrderBy($relation);
            $this->with[$relation] = fn ($query) => $query->orderBy($orderBy);
        }
    }

    private function getOrderBy($strRelation){
        $model = $this->builder->getModel();
        $relations = explode(".", $strRelation);
        foreach ($relations as $key => $relation) {
            $model = $model->$relation()->getModel();
        }

        return $model->getKeyOrderBy();
    }

    private function arrayBuildWith()
    {
        foreach ($this->request->optionalIncludes as $relation => $paramns) {
            if (gettype($relation) == 'integer') $relation = $paramns;
            $relations = explode(",", $relation);
            $this->queryRelation($relations, $paramns);
        }
    }

    private function queryRelation($relations, $paramns)
    {
        foreach ($relations as  $relation) {

            $this->with[$relation] =  function ($query) use ($paramns) {
                if (gettype($paramns) == 'string') return $query->where($this->builder->getModel()->getPrimaryKey(), '<>', null);
                (new QueryString($query, $paramns))->getBuilder();
            };
        }
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
