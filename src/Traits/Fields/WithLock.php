<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait WithLock
{
    protected bool $lock = false;

    public function locked(): static
    {
        $this->lock = true;

        return $this;
    }

    public function isLocked(): bool
    {
        return $this->lock;
    }
}
