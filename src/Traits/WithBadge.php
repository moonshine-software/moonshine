<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;

trait WithBadge
{
    protected Closure|string|int|float|null $badge = null;

    public function badge(Closure|string|int|float|null $value): static
    {
        $this->badge = $value;

        return $this;
    }

    public function hasBadge(): bool
    {
        return filled($this->getBadge());
    }

    public function getBadge(): mixed
    {
        return value($this->badge, $this);
    }
}
