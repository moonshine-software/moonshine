<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Closure;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Resources\CustomPage;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\WithIcon;
use Leeto\MoonShine\Traits\WithLabel;

abstract class MenuSection
{
    use WithIcon;
    use HasCanSee;
    use WithLabel;

    protected string|null $link = null;

    protected Collection $items;

    protected ?Resource $resource = null;

    protected ?CustomPage $page = null;

    protected ?Closure $badge = null;

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
        return ! is_null($this->badge);
    }

    public function getBadge()
    {
        return call_user_func($this->badge);
    }

    public function resource(): ?Resource
    {
        return $this->resource;
    }

    public function page(): ?CustomPage
    {
        return $this->page;
    }

    public function setItems(Collection $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function url(): string
    {
        if ($this->link) {
            return $this->link;
        }

        if ($this->page()) {
            return $this->page()->url();
        }

        return route($this->resource()->routeName('index'));
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
        }

        if ($this->resource()) {
            return request()->routeIs($this->resource()->routeName('*'));
        }

        if ($this->page()) {
            return request()->url() === $this->page()->url();
        }

        return false;
    }
}
