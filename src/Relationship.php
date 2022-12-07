<?php

namespace Cometa\Includes;

use Cometa\Builder\QueryString;
use Illuminate\Http\Request;
use Closure;

class Relationship
{
    private $with;
    private $model;

    public function __construct($model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
        $this->with();
        $this->queryString();
    }

    private function queryString()
    {
        $queryStringToEloquente = new QueryString($this->model, $this->request->except(['filter', 'includes']));
        $this->model = $queryStringToEloquente->getBuilder();
    }

    private function with()
    {
        $this->buildWithIncludes();
        $this->buildWithFilter();

        if ($this->with) {
            $this->model = $this->model->with($this->with);
            $this->buildWhereHas();
        }
    }

    private function buildWithIncludes()
    {
        if (!$this->request->exists('includes'))  return;

        $relationships = explode(',', $this->request->includes);
        foreach ($relationships as $key => $relation) {
            $this->with[] = $relation;
        }
    }

    private function buildWithFilter()
    {
        if (!$this->request->exists('filter'))  return;

        foreach ($this->request->filter as $relation => $paramns) {
            $this->with[$relation] =  function ($query) use ($paramns) {
                (new QueryString($query, $paramns))->getBuilder();
            };
        }
    }

    private function buildWhereHas()
    {
        foreach ($this->with as $relation => $call) {
            if ($call instanceof Closure) {
                $this->model = $this->model->whereHas($relation, $call);
                continue;
            }

            $this->model->whereHas($call, function ($query) {
                $query->where('id', '<>', null);
            });
        }
    }

    public function getModel()
    {
        return $this->model;
    }
}