<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

interface MenuFillerContract
{
    public function getUrl(): string;

    public function isActive(): bool;
}
