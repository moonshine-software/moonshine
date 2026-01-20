<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Support\Enums\Color;

interface HasBadgeContract
{
    /**
     * @param  string|Color|(Closure(mixed $value, static $ctx): string|Color)|null  $color
     * @param  string|(Closure(static $ctx): string)|null  $icon
     *
     */
    public function badge(string|Color|Closure|null $color = null, string|Closure|null $icon = null): static;

    public function isBadge(): bool;

    public function getBadgeColor(mixed $value = null): string;
}
