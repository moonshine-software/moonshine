<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

use Closure;
use MoonShine\Contracts\Core\StatefulContract;

interface MenuManagerContract extends StatefulContract
{
    /**
     * @param  list<MenuElementContract>|MenuElementContract  $data
     */
    public function add(array|MenuElementContract $data): static;

    public function remove(Closure $condition): static;

    /**
     * @param  list<MenuElementContract>|MenuElementContract|Closure  $data
     */
    public function addBefore(Closure $before, array|MenuElementContract|Closure $data): static;

    /**
     * @param  list<MenuElementContract>|MenuElementContract|Closure   $data
     */
    public function addAfter(Closure $after, array|MenuElementContract|Closure $data): static;

    public function topMode(?Closure $condition = null): self;

    /**
     * @param  list<MenuElementContract>|null  $items
     */
    public function all(?iterable $items = null): MenuElementsContract;
}
