<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use Illuminate\Support\Collection;

abstract class DashboardScreen
{
    public function getBlocks(): Collection
    {
        $blocks = collect();

        collect($this->blocks() ?? [])->each(
            function ($item) use ($blocks): void {
                $item = is_string($item) ? new $item() : $item;

                if ($item instanceof DashboardBlock) {
                    $blocks->add($item);
                }
            }
        );

        return $blocks;
    }

    abstract public function blocks(): array;
}
