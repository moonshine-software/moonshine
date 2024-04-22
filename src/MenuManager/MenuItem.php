<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Attributes\Icon;
use MoonShine\Support\Attributes;
use MoonShine\Support\Condition;
use Throwable;

/**
 * @method static static make(Closure|string $label, Closure|MenuFiller|string $filler, string $icon = null, Closure|bool $blank = false)
 */
class MenuItem extends MenuElement
{
    protected string $view = 'moonshine::components.menu.item';

    protected ?Closure $badge = null;

    protected Closure|string|null $url = null;

    protected Closure|bool $blank = false;

    protected ?Closure $whenActive = null;

    protected ActionButton $actionButton;

    final public function __construct(
        Closure|string $label,
        protected Closure|MenuFiller|string $filler,
        string $icon = null,
        Closure|bool $blank = false
    ) {
        $this->setLabel($label);

        if ($icon) {
            $this->icon($icon);
        }

        if ($filler instanceof MenuFiller) {
            $this->resolveFiller($filler);
        } else {
            $this->setUrl($filler);
        }

        $this->blank($blank);

        $this->actionButton = ActionButton::make($label);
    }

    protected function resolveFiller(MenuFiller $filler): void
    {
        $this->setUrl(fn (): string => $filler->url());

        $icon = Attributes::for($filler)
            ->attribute(Icon::class)
            ->attributeProperty('icon')
            ->get();

        if (method_exists($filler, 'getBadge')) {
            $this->badge(fn () => $filler->getBadge());
        }

        if (! is_null($icon) && $this->iconValue() === '') {
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

    public function getBadge(): mixed
    {
        return value($this->badge);
    }

    public function whenActive(Closure $when): static
    {
        $this->whenActive = $when;

        return $this;
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

    public function blank(Closure|bool $blankCondition = true): static
    {
        $this->blank = Condition::boolean($blankCondition, true);

        return $this;
    }

    public function isBlank(): bool
    {
        return $this->blank;
    }

    /**
     * @throws Throwable
     */
    public function isActive(): bool
    {
        $filler = $this->getFiller();

        if ($filler instanceof MenuFiller) {
            return $filler->isActive();
        }

        $path = parse_url($this->url(), PHP_URL_PATH) ?? '/';
        $host = parse_url($this->url(), PHP_URL_HOST) ?? '';

        $isActive = function ($path, $host) {
            if ($path === '/' && request()->host() === $host) {
                return request()->path() === $path;
            }

            if ($this->url() === moonshineRouter()->home()) {
                return request()->fullUrlIs($this->url());
            }

            return request()->fullUrlIs('*' . $this->url() . '*');
        };

        return is_null($this->whenActive)
            ? $isActive($path, $host)
            : value($this->whenActive, $path, $host, $this);
    }

    public function viewData(): array
    {
        if ($this->isBlank()) {
            $this->actionButton = $this->actionButton->customAttributes([
                '_target' => '_blank',
            ]);
        }

        if (! $this->isTopMode()) {
            $this->actionButton = $this->actionButton->customAttributes([
                'x-data' => 'navTooltip',
                '@mouseenter' => 'toggleTooltip',
            ]);
        }

        $viewData = [
            'url' => $this->url(),
        ];

        if ($this->hasBadge() && $badge = $this->getBadge()) {
            $viewData['badge'] = $badge;
        }

        $viewData['actionButton'] = $this->actionButton
            ->setUrl($this->url())
            ->customView('moonshine::components.menu.item-link', [
                'url' => $this->url(),
                'label' => $this->label(),
                'icon' => $this->iconValue() ? $this->getIcon(6) : '',
                'top' => $this->isTopMode(),
                'badge' => $viewData['badge'] ?? '',
            ]);

        return $viewData;
    }
}
