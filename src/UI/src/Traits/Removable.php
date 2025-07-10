<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;

trait Removable
{
    protected bool $removable = false;

    /**
     * @var array<string, mixed>
     */
    protected array $removableAttributes = [];

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function removable(
        Closure|bool|null $condition = null,
        array $attributes = []
    ): static {
        $this->removable = value($condition, $this) ?? true;
        $this->removableAttributes = $attributes;

        return $this;
    }

    public function getRemovableAttributes(): ComponentAttributesBagContract
    {
        return new MoonShineComponentAttributeBag($this->removableAttributes);
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }
}
