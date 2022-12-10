<?php

namespace GrupoCometa\Includes\Query;

use GrupoCometa\Builder\QueryString;
use Illuminate\Http\Request;

class IncludesRelationship 
{
    public function __construct(private $model, private Request $request)
    {
        $function = gettype($this->request->includes) . 'BuildWith';
        $this->$function();
        $this->with();
    }

    private function with()
    {
        if (!$this->with) return;
        $this->model = $this->model->with($this->with);
    }

    private function  stringBuildWith()
    {
        $relationships = explode(',', $this->request->includes);
        foreach ($relationships as $relation) {

            $this->with[$relation] = fn ($query) => $query->orderBy('id');
            $this->model = $this->model->whereHas($relation, $this->with[$relation]);
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
                if (gettype($paramns) == 'string') return $query->where('id', '<>', null);
                (new QueryString($query, $paramns))->getBuilder();
            };

            $this->model = $this->model->whereHas($relation, $this->with[$relation]);
        }
    }

    public function getModel()
    {
        return $this->model;
    }
}
