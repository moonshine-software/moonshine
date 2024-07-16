<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuElementsContract;

/**
 * @extends Collection<int, MenuElementContract>
 */
final class MenuElements extends Collection implements MenuElementsContract
{
    public function topMode(?Closure $condition = null): self
    {
        return $this->transform(static function (MenuElementContract $item) use ($condition): MenuElementContract {
            $item = clone $item;

            if ($item instanceof MenuGroup) {
                $item->setItems(
                    $item->getItems()->topMode($condition)
                );
            }

            return $item->topMode($condition);
        });
    }

    public function onlyVisible(): self
    {
        return $this->filter(static function (MenuElementContract $item): bool {
            if ($item instanceof MenuGroup) {
                $item->setItems(
                    $item->getItems()->onlyVisible()
                );
            }

            return $item->isSee();
        });
    }
}
