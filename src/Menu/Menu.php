<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Illuminate\Support\Collection;

final class Menu
{
    protected static ?Collection $menu = null;

    public static function register(Collection $data): void
    {
        self::$menu = $data;
    }

    public static function all(): ?Collection
    {
        return self::$menu;
    }
}
