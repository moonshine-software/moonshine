<?php
namespace Leeto\MoonShine\Dashboard;

use Illuminate\Support\Collection;
use Leeto\MoonShine\MoonShine;

class Dashboard
{
    protected Collection|null $blocks = null;

    public function registerBlocks(array $data): void
    {
        $this->blocks = collect();

        collect($data)->each(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            if($item instanceof DashboardBlock) {
                $this->blocks->add($item);
            }
        });
    }

    public function getBlocks(): Collection|null
    {
        $class = MoonShine::namespace('\Dashboard');
        $blocks = class_exists($class) ? (new $class())->getBlocks() : collect();

        return $blocks->isNotEmpty() ? $blocks : $this->blocks;
    }
}
