<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Support\Collection;

abstract class DashboardScreen
{
    abstract public function blocks(): array;

    public function getBlocks(): Collection
    {
        return collect($this->blocks() ?? [])->each(function ($item): void {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof DashboardBlock) {
                $item->add($item);
            }
        });
    }
}
