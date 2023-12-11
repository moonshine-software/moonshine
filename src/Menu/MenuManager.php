<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use Illuminate\Support\Collection;

class MenuManager
{
    protected static Closure|Collection|array|null $menu = null;

    public static function register(Closure|array|Collection|null $data): void
    {
        self::$menu = $data;
    }

    public static function all(): ?Collection
    {
        return collect(value(self::$menu, moonshineRequest()))?->filter(function (MenuElement $item): bool {
            if ($item instanceof MenuGroup) {
                $item->setItems(
                    $item->items()->filter(
                        fn (MenuElement $child): bool => $child->isSee(moonshineRequest())
                    )
                );
            }
            return $item->isSee(moonshineRequest());
        });
    }
}
