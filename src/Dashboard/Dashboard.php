<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Support\Collection;
use Leeto\MoonShine\MoonShine;

final class Dashboard
{
    protected static ?Collection $blocks = null;

    public static function blocks(array $data): void
    {
        self::$blocks = collect();

        collect($data)->each(function ($item): void {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof DashboardBlock) {
                self::$blocks->add($item);
            }
        });
    }

    public static function getBlocks(): ?Collection
    {
        $class = MoonShine::namespace('\Dashboard');

        $blocks = class_exists($class) ? (new $class())->getBlocks() : collect();

        return $blocks->isNotEmpty() ? $blocks : self::$blocks;
    }
}
