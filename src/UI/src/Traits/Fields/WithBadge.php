<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Support\Enums\Color;

trait WithBadge
{
    protected bool $isBadge = false;

    protected string|Color $badgeColor = Color::GRAY;

    protected ?string $badgeIcon = null;

    protected ?Closure $badgeColorCallback = null;

    protected ?Closure $badgeIconCallback = null;

    /**
     * @param  string|Color|(Closure(mixed $value, static $ctx): string|Color)|null  $color
     * @param  string|(Closure(mixed $value, static $ctx): string)|null  $icon
     */
    public function badge(string|Color|Closure|null $color = null, string|Closure|null $icon = null): static
    {
        if ($color instanceof Closure) {
            $this->badgeColorCallback = $color;
        } elseif (! \is_null($color)) {
            $this->badgeColor = $color;
        }

        if ($icon instanceof Closure) {
            $this->badgeIconCallback = $icon;
        } elseif (! \is_null($icon)) {
            $this->badgeIcon = $icon;
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
        $color = \is_null($this->badgeColorCallback)
            ? $this->badgeColor
            : \call_user_func($this->badgeColorCallback, $value ?? $this->toValue(withDefault: false), $this);


        return $color instanceof Color ? $color->value : $color;
    }

    public function getBadgeIcon(mixed $value = null): ?string
    {
        return \is_null($this->badgeIconCallback)
            ? $this->badgeIcon
            : \call_user_func($this->badgeIconCallback, $value ?? $this->toValue(withDefault: false), $this);
    }
}
