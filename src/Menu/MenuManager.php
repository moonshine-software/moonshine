<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Illuminate\Support\Collection;

class MenuManager
{
    protected static ?Collection $menu = null;

    public static function register(Collection $data): void
    {
        self::$menu = $data;
    }

    public static function all(): ?Collection
    {
        return self::$menu?->filter(function (MenuElement $item) {
            if ($item instanceof MenuGroup) {
                $item->setItems(
                    $item->items()->filter(
                        fn (MenuElement $child) => $child->isSee(moonshineRequest())
                    )
                );
            }

            return $item->isSee(moonshineRequest());
        });
    }
}
