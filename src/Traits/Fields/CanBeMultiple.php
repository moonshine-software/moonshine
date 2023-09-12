<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use MoonShine\Support\Condition;

trait CanBeMultiple
{
    protected bool $multiple = false;

    public function multiple(Closure|bool|null $condition = null): static
    {
        $this->multiple = Condition::boolean($condition, true);

        return $this->setAttribute('multiple', $this->multiple);
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }
}
