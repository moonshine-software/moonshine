<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use MoonShine\Attributes\Icon;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Support\Attributes;

/**
 * @method static static make(Closure|string $label, Closure|MenuFiller|string $filler, string $icon = null)
 */
class MenuItem extends MenuElement
{
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

        $icon = Attributes::for($filler)
            ->attribute(Icon::class)
            ->attributeProperty('icon')
            ->get();

        if (method_exists($filler, 'getBadge')) {
            $this->badge(fn () => $filler->getBadge());
        }

        if ($this->iconValue() === '' && ! is_null($icon)) {
            $this->icon($icon);
        }
    }

    public function getFiller(): MenuFiller|Closure|string
    {
        return $this->filler;
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

    public function getBadge(): ?mixed
    {
        return value($this->badge);
    }
}
