<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait SelectTransform
{
    protected bool $select = false;

    public function select(): static
    {
        $this->select = true;

        return $this->multiple()->searchable();
    }

    public function isSelect(): bool
    {
        return $this->select;
    }
}
