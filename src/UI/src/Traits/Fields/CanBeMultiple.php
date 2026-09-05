<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\UI\Contracts\HasDefaultValueContract;

trait CanBeMultiple
{
    protected bool $multiple = false;

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function multiple(Closure|bool|null $condition = null): static
    {
        $this->multiple = value($condition, $this) ?? true;

        if ($this instanceof HasDefaultValueContract && \is_null($this->getDefault())) {
            $this->default([]);
        }

        return $this->setAttribute('multiple', $this->multiple);
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }
}
