<?php

declare(strict_types=1);

namespace MoonShine\Menu;

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

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function isItem(): bool
    {
        return $this instanceof MenuItem;
    }

    /**
     * @throws Throwable
     */
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

        if ($this->isItem()) {
            $filler = $this->getFiller();

            if ($filler instanceof MenuFiller) {
                return $filler->isActive();
            }

            $path = parse_url((string) $this->url(), PHP_URL_PATH) ?? '/';

            if ($path === '/') {
                return request()->path() === $path;
            }

            return request()->fullUrlIs($this->url() . '*');
        }

        return false;
    }
}
