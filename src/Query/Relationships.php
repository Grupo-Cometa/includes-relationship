<?php

namespace GrupoCometa\Includes\Query;

use Illuminate\Http\Request;
use GrupoCometa\Builder\QueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Relationships
{
    private $builders = [
        'includes' => IncludesRelationship::class,
        'count' =>  CountRelationship::class
    ];

    private Builder $builder;

    public function __construct(private InterfaceInclude & Model $model, private Request $request)
    {
        $this->filterModel();
        $this->bootstrap();
    }

    private function filterModel()
    {
        $queryString = (new QueryString($this->model, $this->request->all()));
        $this->builder = $queryString->getBuilder();
    }

    private function bootstrap()
    {
        foreach ($this->builders as $key => $builder) {
            if (!$this->request->exists($key)) continue;
            $this->builder = (new $builder($this->builder, $this->request))->getBuilder();
        }

        $this->builder = (new OrderBy($this->builder, $this->request))->getBuilder();
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
