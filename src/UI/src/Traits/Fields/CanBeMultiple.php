<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;

trait CanBeMultiple
{
    protected bool $multiple = false;

    public function multiple(Closure|bool|null $condition = null): static
    {
        $this->multiple = value($condition, $this) ?? true;

        return $this->setAttribute('multiple', $this->multiple);
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }
}
