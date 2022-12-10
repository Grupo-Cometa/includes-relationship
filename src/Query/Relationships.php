<?php

namespace GrupoCometa\Includes\Query;

use Illuminate\Http\Request;

class Relationships
{
    private $builders = [
        'includes' => IncludesRelationship::class,
        'count' =>  CountRelationship::class
    ];

    public function __construct(private $model, private Request $request)
    {
        $this->bootstrap();
    }

    private function bootstrap()
    {
        foreach ($this->builders as $key => $builder) {
            if (!$this->request->exists($key)) continue;
            $this->model = (new $builder($this->model, $this->request))->getModel();
        }

        $this->model = (new OrderBy($this->model, $this->request))->getModel();
    }

    public function getModel()
    {
        return $this->model;
    }
}