<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

use MoonShine\UI\Components\Icon;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function getIcon(
        int $size = 8,
        string $color = '',
        array $attributes = []
    ): string {
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

        return (string) rescue(
            static fn () => $icon->render(),
            rescue: static fn (): string => '',
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
