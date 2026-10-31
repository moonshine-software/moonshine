<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Services;

use MoonShine\Tests\Fixtures\Models\Item;

final class SaveItemHandler
{
    public array $data = [];

    public function __invoke(Item $item, array $data): Item
    {
        $this->data = $data;
        $item->forceFill($data)->save();

        return $item;
    }
}
