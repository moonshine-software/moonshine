<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Illuminate\Support\Collection;

final class Menu
{
    protected ?Collection $menu = null;

    public function register(Collection $data): void
    {
        $this->menu = $data;
    }

    public function all(): ?Collection
    {
        return $this->menu;
    }
}
