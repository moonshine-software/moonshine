<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

interface MenuElementContract
{
    public function isActive(): bool;
}
