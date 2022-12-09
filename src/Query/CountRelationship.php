<?php

namespace GrupoCometa\Includes\Query;

use GrupoCometa\Builder\QueryString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CountRelationship {

    private array|string $count;

    public function __construct(private Model $model, private Request $request)
    {
        $function = gettype($this->request->count) . 'BuildWithCount';
        $this->$function();
    }

    private function arrayBuildWithCount()
    {
        foreach ($this->request->count as $relation => $paramns) {

            if (gettype($relation) == 'integer') $relation = $paramns;

            $this->count[$relation] =  function ($query) use ($paramns) {
                if (gettype($paramns) == 'string') return $query->where('id', '<>', null);
                (new QueryString($query, $paramns))->getBuilder();
            };
        }

        $this->model = $this->model->withCount($this->count);
    }

    private function  stringBuildWithCount()
    {
        $this->count = explode(',', $this->request->count);
        $this->model = $this->model->withCount($this->count);
    }

    public function getModel()
    {
        return $this->model;
    }
}