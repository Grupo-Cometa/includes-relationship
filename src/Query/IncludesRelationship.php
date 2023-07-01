<?php

namespace GrupoCometa\Includes\Query;

use GrupoCometa\Builder\QueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IncludesRelationship
{
    private $with;
    public function __construct(private Builder|HasMany|BelongsTo|HasOne|BelongsToMany $builder, private Request $request)
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
            $orderBy = $this->getKeyOrderBy($relation);
            $this->with[$relation] = fn ($query) => $query->orderBy($orderBy);
            $this->builder = $this->builder->whereHas($relation, $this->with[$relation]);
        }
    }

    private function getKeyOrderBy($relation)
    {
        $relations = explode('.', $relation);

        $model = $this->builder->getModel();
        foreach ($relations as $relationModel) {
            $model =  $model->$relationModel()->getModel();
        }
        return "{$model->getTable()}.{$model->getKeyOrderBy()}";
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
