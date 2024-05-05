<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\Contracts\View\View;
use MoonShine\Components\Icon;

trait WithIcon
{
    protected ?string $icon = null;

    protected bool $customIcon = false;

    protected ?string $iconPath = null;

    public function icon(string $icon, bool $custom = false, ?string $path = null): static
    {
        $this->icon = $icon;
        $this->customIcon = $custom;
        $this->iconPath = $path;

        return $this;
    }

    public function getIcon(
        int $size = 8,
        string $color = '',
        array $attributes = []
    ): View|string {
        if ($this->getIconValue() === '') {
            return '';
        }

        $icon = Icon::make(
            $this->getIconValue(),
            $size,
            $color,
            $this->getIconPath()
        )->customAttributes($attributes);

        if($this->isCustomIcon()) {
            $icon->custom();
        }

        return rescue(
            fn () => $icon->render(),
            rescue: fn (): string => '',
            report: false
        );
    }

    public function isCustomIcon(): bool
    {
        return $this->customIcon;
    }

    public function getIconPath(): ?string
    {
        return $this->iconPath;
    }

    public function getIconValue(): string
    {
        return $this->icon ?? '';
    }
}
