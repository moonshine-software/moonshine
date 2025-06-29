<?php

declare(strict_types=1);

namespace MoonShine\Support\Concerns;

/**
 * @phpstan-ignore trait.unused
 */
trait MenuFillerConcern
{
    public function getTitle(): string
    {
        return '';
    }

    public function getUrl(): string
    {
        return '';
    }

    public function isActive(): bool
    {
        return true;
    }

    public function skipMenu(): bool
    {
        return false;
    }

    public function getGroup(): ?string
    {
        return null;
    }

    public function getGroupIcon(): ?string
    {
        return null;
    }

    public function getBadge(): ?string
    {
        return null;
    }

    public function canSee(): bool
    {
        return true;
    }

    public function getIcon(): ?string
    {
        return null;
    }

    public function getPosition(): int
    {
        return 1;
    }
}
