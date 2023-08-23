<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Menu;

interface MenuFiller
{
    public function url(): string;

    public function isActive(): bool;
}
