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

    public function all(): Collection
    {
        return $this->prepareMenu(value($this->menu, moonshineRequest()));
    }

    public function hasForceActive(): bool
    {
        return $this->all()->contains(function (MenuElement $item) {
            if($item->isForceActive()) {
                return true;
            }

            if($item instanceof MenuGroup) {
                return $item->items()->contains(fn (MenuElement $child) => $child->isForceActive());
            }

            return false;
        });
    }

    public function prepareMenu(Collection|array|null $items = []): Collection
    {
        return collect($items)->filter(function (MenuElement $item): bool {
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
