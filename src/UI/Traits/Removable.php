<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use MoonShine\UI\Components\MoonShineComponentAttributeBag;

trait Removable
{
    protected bool $removable = false;

    protected array $removableAttributes = [];

    public function removable(
        Closure|bool|null $condition = null,
        array $attributes = []
    ): static {
        $this->removable = value($condition, $this) ?? true;
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
