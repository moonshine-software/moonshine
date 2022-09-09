<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use JsonSerializable;
use Leeto\MoonShine\Exceptions\MenuException;
use Leeto\MoonShine\Traits\Makeable;

final class MenuGroup extends MenuSection implements JsonSerializable
{
    use Makeable;

    final public function __construct(
        string $title,
        array $items
    ) {
        $this->title = $title;
        $this->items = collect($items)->map(function ($item) {
            if (!$item instanceof MenuSection) {
                throw MenuException::onlyMenuItemAllowed();
            }

            return $item;
        });
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title(),
            'icon' => $this->getIcon(),
            'items' => $this->items(),
        ];
    }
}
