<?php

namespace GrupoCometa\Includes\Query;

interface InterfaceInclude
{
   public function getKeyOrderBy(): string;
   public function getPrimaryKey(): string;
   public function caseSensitive(): bool;
}
