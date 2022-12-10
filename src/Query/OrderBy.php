<?php

namespace GrupoCometa\Includes\Query;

use Illuminate\Http\Request;

class OrderBy 
{
    public function __construct(private $model, private Request $request)
    {
        if (!$this->request->exists('orderBy')) {
            $this->model =  $this->model->orderBy('id');
            return;
        }

        foreach ( $this->request->orderBy as $key => $order) {
            if (gettype($order) == 'string') {
                $order = explode(',', $order);
                $this->buildOrderBy($order, $key, $this);
                continue;
            }

           $this->buildOrderBy($order, $key);
        }
    }

    private function buildOrderBy(array $orders, $type)
    {
        foreach ($orders as $order) {
            $this->model =  $this->model->orderBy($order, $type);
        }
    }

    public function getModel()
    {
        return $this->model;
    }
}
