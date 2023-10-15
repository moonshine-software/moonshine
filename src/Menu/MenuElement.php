<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use Throwable;

abstract class MenuElement
{
    use Makeable;
    use WithIcon;
    use HasCanSee;
    use WithLabel;

    protected Closure|string|null $url = null;

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function isItem(): bool
    {
        return $this instanceof MenuItem;
    }

    public function setUrl(string|Closure|null $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function url(): string
    {
        return value($this->url);
    }

    /**
     * @throws Throwable
     */
    public function isActive(): bool
    {
        if ($this instanceof MenuGroup) {
            foreach ($this->items() as $item) {
                if ($item->isActive()) {
                    return true;
                }
            }

            return false;
        }

        if ($this->isItem()) {
            $filler = $this instanceof MenuItem
                ? $this->getFiller()
                : null;

            if ($filler instanceof MenuFiller) {
                return $filler->isActive();
            }

            $path = parse_url($this->url(), PHP_URL_PATH) ?? '/';
            $host = parse_url($this->url(), PHP_URL_HOST) ?? '';

            if ($path === '/' && request()->host() === $host) {
                return request()->path() === $path;
            }

            return request()->fullUrlIs($this->url() . '*');
        }

        return false;
    }
}
