<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Illuminate\Support\Collection;

class Menu
{
    protected static ?Collection $menu = null;

    public static function register(Collection $data): void
    {
        self::$menu = $data;
    }

    public static function all(): ?Collection
    {
        return self::$menu?->filter(function ($item) {
            if ($item->isGroup()) {
                $item->setItems(
                    $item->items()->filter(
                        fn ($subItem) => $subItem->isSee(moonshineRequest())
                    )
                );
            }

            return $item->isSee(moonshineRequest());
        });
    }
}
