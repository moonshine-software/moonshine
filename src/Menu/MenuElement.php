<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Support\Condition;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use Throwable;

abstract class MenuElement
{
    use Makeable;
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use WithComponentAttributes;

    protected Closure|string|null $url = null;

    protected Closure|bool $blank = false;

    protected Closure|bool|null $forceActive = null;

    protected array $customLinkAttributes = [];

    protected ?string $customView = null;

    public function forceActive(Closure|bool $forceActive): static
    {
        $this->forceActive = $forceActive;

        return $this;
    }

    public function isForceActive(): bool
    {
        return ! is_null($this->forceActive);
    }

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function isItem(): bool
    {
        return $this instanceof MenuItem;
    }

    public function setUrl(string|Closure|null $url, Closure|bool $blank = false): static
    {
        $this->url = $url;

        $this->blank($blank);

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function url(): string
    {
        return value($this->url) ?? '';
    }

    /**
     * @throws Throwable
     */
    public function isActive(): bool
    {
        if ($this->isForceActive() && value($this->forceActive) === true) {
            return true;
        }

        if ($this instanceof MenuGroup) {
            foreach ($this->items() as $item) {
                if ($item->isActive()) {
                    return true;
                }
            }

            return false;
        }

        if(moonshineMenu()->hasForceActive()) {
            return false;
        }

        if (! $this->isItem()) {
            return false;
        }

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

        if ($this->url() === moonshineRouter()->home()) {
            return request()->fullUrlIs($this->url());
        }

        return request()->fullUrlIs($this->url() . '*');
    }

    public function blank(Closure|bool $blankCondition = true): static
    {
        $this->blank = Condition::boolean($blankCondition, true);

        return $this;
    }

    public function isBlank(): bool
    {
        return $this->blank;
    }

    public function customLinkAttributes(array $attributes): static
    {
        if (isset($attributes['class'])) {
            $this->customLinkAttributes['class'] = $this->uniqueAttribute(
                old: $this->customLinkAttributes['class'] ?? '',
                new: $attributes['class']
            );

            unset($attributes['class']);
        }

        $this->customLinkAttributes = array_merge(
            $this->customLinkAttributes,
            $attributes
        );

        return $this;
    }

    public function linkAttributes(): ComponentAttributeBag
    {
        return new ComponentAttributeBag(
            $this->customLinkAttributes
        );
    }

    public function customView(string $path): static
    {
        $this->customView = $path;

        return $this;
    }

    public function getCustomView(): ?string
    {
        return $this->customView;
    }

    public function hasCustomView(): bool
    {
        return !is_null($this->customView);
    }
}
