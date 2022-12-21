<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Illuminate\Support\Collection;

class Menu
{
    protected Collection|null $menu = null;

    public function register(Collection $data): void
    {
        $this->menu = $data;
    }

    public function all(): Collection|null
    {
        return $this->menu->filter(function ($item) {
            if ($item->isGroup()) {
                $item->setItems(
                    $item->items()->filter(fn ($subItem) => $subItem->isSee(request()))
                );
            }

            return $item->isSee(request());
        });
    }
}
