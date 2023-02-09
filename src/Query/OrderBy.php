<?php

namespace GrupoCometa\Includes\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrderBy
{
    public function __construct(private Builder $builder, private Request $request)
    {
        if (!$this->request->exists('orderBy')) {
            $orderBy = $this->builder->getModel()->getKeyOrderBy();
            $this->builder =  $this->builder->orderBy($orderBy);
            return;
        }

        foreach ($this->request->orderBy as $key => $order) {
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
            $this->builder =  $this->builder->orderBy($order, $type);
        }
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }
}
