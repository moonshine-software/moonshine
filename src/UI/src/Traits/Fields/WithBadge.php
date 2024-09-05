<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Support\Enums\Color;

trait WithBadge
{
    protected bool $isBadge = false;

    protected string|Color $badgeColor = Color::GRAY;

    protected ?Closure $badgeColorCallback = null;

    public function badge(string|Color|Closure|null $color = null): static
    {
        if ($color instanceof Closure) {
            $this->badgeColorCallback = $color;
        } elseif (! is_null($color)) {
            $this->badgeColor = $color;
        }

        $this->isBadge = true;

        return $this;
    }

    public function isBadge(): bool
    {
        return $this->isBadge;
    }

    public function getBadgeColor(mixed $value = null): string
    {
        $color = is_null($this->badgeColorCallback)
            ? $this->badgeColor
            : value($this->badgeColorCallback, $value ?? $this->toValue(withDefault: false), $this);


        return $color instanceof Color ? $color->value : $color;
    }
}
