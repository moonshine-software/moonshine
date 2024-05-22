<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;

trait WithBadge
{
    protected bool $isBadge = false;

    protected string $badgeColor = 'gray';

    protected ?Closure $badgeColorCallback = null;

    public function badge(string|Closure|null $color = null): static
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

    public function badgeColor(mixed $value = null): string
    {
        if (! is_null($this->badgeColorCallback)) {
            return value($this->badgeColorCallback, $value ?? $this->toValue(withDefault: false), $this);
        }

        return $this->badgeColor;
    }
}
