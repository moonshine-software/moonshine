<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use JsonSerializable;
use Leeto\MoonShine\Contracts\ResourceContract;
use Leeto\MoonShine\Exceptions\MenuException;
use Leeto\MoonShine\Traits\Makeable;

final class MenuGroup extends MenuSection implements JsonSerializable
{
    use Makeable;

    final public function __construct(string $title, array $items, string $icon = null)
    {
        $this->title = $title;
        $this->items = collect($items)->map(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            throw_if(
                !$item instanceof MenuItem && !$item instanceof ResourceContract,
                new MenuException('An object of the MenuItem|Resource class is required')
            );

            if ($item instanceof ResourceContract) {
                return new MenuItem($item->title(), $item);
            }

            return $item;
        });

        if ($icon) {
            $this->icon($icon);
        }
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
