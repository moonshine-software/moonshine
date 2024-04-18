<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int, MenuElement>
 */
final class MenuElements extends Collection
{
    public function onlyVisible(): self
    {
        return $this->filter(function (MenuElement $item): bool {
            if ($item instanceof MenuGroup) {
                $item->setItems(
                    $item->items()->onlyVisible()
                );
            }

            return $item->isSee(moonshineRequest());
        });
    }
}
