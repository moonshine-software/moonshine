<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Support\Collection;

abstract class DashboardScreen
{
    abstract public function blocks(): array;

    public function getBlocks(): Collection
    {
        $blocks = collect();

        collect($this->blocks() ?? [])->each(function ($item) use ($blocks) {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof DashboardBlock) {
                $blocks->add($item);
            }
        });

        return $blocks;
    }
}
