<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use MoonShine\Contracts\Menu\MenuFiller;
use Throwable;

/**
 * @method static static make(Closure|string $label, Closure|MenuFiller|string $filler, string $icon = null)
 */
class MenuItem extends MenuElement
{
    protected Closure|string|null $url = null;

    protected ?Closure $badge = null;

    final public function __construct(
        Closure|string $label,
        protected Closure|MenuFiller|string $filler,
        string $icon = null
    ) {
        $this->setLabel($label);

        if ($icon) {
            $this->icon($icon);
        }

        if ($filler instanceof MenuFiller) {
            $this->resolveMenuFiller($filler);
        } else {
            $this->setUrl($filler);
        }
    }

    protected function resolveMenuFiller(MenuFiller $filler): void
    {
        $this->setUrl(fn (): string => $filler->url());

        if (method_exists($filler, 'getBadge')) {
            $this->badge(fn () => $filler->getBadge());
        }

        if ($this->iconValue() === '' && method_exists($filler, 'getIcon')) {
            $this->icon($filler->getIcon());
        }
    }

    public function getFiller(): MenuFiller|string
    {
        return is_closure($this->filler)
            ? call_user_func($this->filler)
            : $this->filler;
    }

    public function badge(Closure $callback): static
    {
        $this->badge = $callback;

        return $this;
    }

    public function hasBadge(): bool
    {
        if (is_null($this->badge)) {
            return false;
        }

        $badge = $this->getBadge();

        return  $badge !== false && ! is_null($badge);
    }

    public function getBadge(): ?string
    {
        return call_user_func($this->badge);
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
        return is_closure($this->url)
            ? call_user_func($this->url)
            : $this->url;
    }
}
