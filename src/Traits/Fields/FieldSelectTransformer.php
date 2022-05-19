<?php

namespace Leeto\MoonShine\Traits\Fields;

trait FieldSelectTransformer
{
    protected bool $select = false;

    public function select(): static
    {
        $this->select = true;

        return $this;
    }

    public function isSelect(): bool
    {
        return $this->select;
    }
}