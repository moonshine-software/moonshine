<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Support\Enums\Color;

interface HasBadgeContract
{
    public function badge(string|Color|Closure|null $color = null): static;

    public function isBadge(): bool;

    public function getBadgeColor(mixed $value = null): string;
}
