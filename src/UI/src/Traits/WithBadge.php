<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;

trait WithBadge
{
    /** @var Closure(static): (string|int|float|false|null)|string|int|float|false|null */
    protected Closure|string|int|float|false|null $badge = null;

    /** @param Closure(static): (string|int|float|false|null)|string|int|float|false|null $value */
    public function badge(Closure|string|int|float|false|null $value): static
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
