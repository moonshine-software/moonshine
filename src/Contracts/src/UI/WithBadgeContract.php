<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface WithBadgeContract
{
    public function badge(Closure|string|int|float|false|null $value): static;

    public function hasBadge(): bool;

    public function getBadge(): string|int|float|false;
}
