<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use Illuminate\Support\Collection;

class MenuManager
{
    protected Closure|Collection|array|null $menu = null;

    public function register(Closure|array|Collection|null $data): void
    {
        $this->menu = $data;
    }

    public function all(): ?Collection
    {
        return collect(value($this->menu, moonshineRequest()))?->filter(function (MenuElement $item): bool {
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
