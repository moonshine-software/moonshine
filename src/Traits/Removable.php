<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\Support\Condition;

trait Removable
{
    protected bool $removable = false;

    public function removable(Closure|bool|null $condition = null): static
    {
        $this->removable = Condition::boolean($condition, true);

        return $this;
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }
}
