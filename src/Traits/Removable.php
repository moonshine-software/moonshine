<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\Support\Condition;
use MoonShine\Support\MoonShineComponentAttributeBag;

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

    public function getRemovableAttributes(): MoonShineComponentAttributeBag
    {
        return new MoonShineComponentAttributeBag($this->removableAttributes);
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }
}
