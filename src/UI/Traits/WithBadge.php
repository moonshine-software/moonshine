<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

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
        return $this->badge !== null;
    }

    public function getBadge(): string|int|float|false
    {
        $badge = value($this->badge, $this);

        return filled($badge) ? $badge : false;
    }
}
