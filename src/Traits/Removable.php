<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait Removable
{
    protected bool $removable = false;

    public function removable(): static
    {
        $this->removable = true;

        return $this;
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }
}
