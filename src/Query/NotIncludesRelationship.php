<?php

namespace GrupoCometa\Includes\Query;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

class NotIncludesRelationship
{
    public function __construct(private Builder|HasMany|BelongsTo|HasOne|BelongsToMany $builder, private Request $request)
    {
        $this->buildNotWith();
    }

    private function  buildNotWith()
    {
        $relationships = explode(',', $this->request->notIncludes);
        foreach ($relationships as $relation) {
            $this->builder = $this->builder->whereDoesntHave($relation);
        }
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
