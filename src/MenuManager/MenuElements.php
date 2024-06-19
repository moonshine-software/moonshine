<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int, MenuElement>
 */
final class MenuElements extends Collection
{
    public function topMode(?Closure $condition = null): self
    {
        return $this->transform(static function (MenuElement $item) use ($condition): MenuElement {
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
        return $this->filter(static function (MenuElement $item): bool {
            if ($item instanceof MenuGroup) {
                $item->setItems(
                    $item->getItems()->onlyVisible()
                );
            }

            return $item->isSee(
                moonshine()->getRequest()
            );
        });
    }
}
