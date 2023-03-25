<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Illuminate\Support\Collection;
use Leeto\MoonShine\MoonShineRequest;

class Menu
{
    protected static ?Collection $menu = null;

    public static function register(Collection $data): void
    {
        self::$menu = $data;
    }

    public static function all(): Collection|null
    {
        $request = app(MoonShineRequest::class);

        return self::$menu?->filter(function ($item) use ($request) {
            if ($item->isGroup()) {
                $item->setItems(
                    $item->items()->filter(fn ($subItem) => $subItem->isSee($request))
                );
            }

            return $item->isSee($request);
        });
    }
}
