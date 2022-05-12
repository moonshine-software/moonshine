<?php

namespace Leeto\MoonShine\Menu;


use Illuminate\Support\Collection;

class Menu
{
    protected Collection|null $menu = null;

    public function register(Collection $data): void
    {
        $this->menu = $data;
    }

    public function get(): Collection|null
    {
        return $this->menu;
    }
}