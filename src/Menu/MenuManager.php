<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Menu\MenuFiller;

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
                        function (MenuElement $child): bool {
                            if($child->getFiller() instanceof MenuFiller) {
                                return $child->isSee(moonshineRequest()) && $child->getFiller()->canSee(moonshineRequest());
                            }
                            return $child->isSee(moonshineRequest());
                        }
                    )
                );
            } else if($item->getFiller() instanceof MenuFiller) {
                return $item->isSee(moonshineRequest()) && $item->getFiller()->canSee(moonshineRequest());
            }
            return $item->isSee(moonshineRequest());
        });
    }
}
