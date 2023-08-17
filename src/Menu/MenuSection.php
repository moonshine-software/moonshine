<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use Throwable;

abstract class MenuSection
{
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use HasResource;

    protected Collection $items;

    protected ?Closure $badge = null;

    protected Closure|string|null $link = null;

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

    public function setItems(Collection $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function setLink(string|Closure|null $link): static
    {
        $this->link = $link;

        return $this;
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

        $path = parse_url($this->url(), PHP_URL_PATH) ?? '/';

        if ($path === '/') {
            return request()->path() === $path;
        }

        if($this->hasResource()) {
            return request()->route('resourceUri')
                === $this->getResource()->uriKey();
        }

        return request()->fullUrlIs($this->url() . '*');
    }

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * @throws Throwable
     */
    public function url(): string
    {
        if ($this->link) {
            return is_callable($this->link)
                ? call_user_func($this->link)
                : $this->link;
        }

        return $this->getResource()
            ->getPages()
            ->first()
            ->route();
    }
}
