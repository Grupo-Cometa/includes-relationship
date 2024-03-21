<?php

namespace GrupoCometa\Includes\Query;

trait TraitInclude
{
    public function getKeyOrderBy(): string
    {
        if (property_exists($this, 'defaultOrderBy')) {
            return $this->defaultOrderBy;
        }
        return  $this->getPrimaryKey();
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function caseSensitive(): bool
    {
        return true;
    }
}
