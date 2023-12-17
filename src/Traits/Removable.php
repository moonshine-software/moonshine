<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Support\Condition;

trait Removable
{
    protected bool $removable = false;

    protected array $removableAttributes = [];

    public function removable(
        Closure|bool|null $condition = null,
        array $attributes = []
    ): static {
        $this->removable = Condition::boolean($condition, true);
        $this->removableAttributes = $attributes;

        return $this;
    }

    public function getRemovableAttributes(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->removableAttributes);
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }
}
