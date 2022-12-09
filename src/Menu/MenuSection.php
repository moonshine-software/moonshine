<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\WithIcon;

abstract class MenuSection
{
    use WithIcon;

    protected string $title;

    protected Collection $items;

    protected Resource $resource;

    protected ?Closure $canSeeCallback = null;

    protected ?Closure $badge = null;

    public function title(): string
    {
        return $this->title;
    }

    public function items(): Collection
    {
        return $this->items;
    }

    public function badge(Closure $callback): static
    {
        $this->badge = $callback;

        return $this;
    }

    public function hasBadge(): bool
    {
        return is_callable($this->badge);
    }

    public function getBadge()
    {
        return call_user_func($this->badge);
    }

    public function resource(): Resource
    {
        return $this->resource;
    }

    public function setItems(Collection $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function canSee(Closure $callback): static
    {
        $this->canSeeCallback = $callback;

        return $this;
    }

    public function isSee(Request $request)
    {
        return is_callable($this->canSeeCallback)
            ? call_user_func($this->canSeeCallback, $request)
            : true;
    }

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function isActive(): bool
    {
        if ($this->isGroup()) {
            foreach ($this->items() as $item) {
                if ($item->isActive()) {
                    return true;
                }
            }

            return false;
        } else {
            return request()->routeIs($this->resource()->routeName('*'));
        }
    }
}
