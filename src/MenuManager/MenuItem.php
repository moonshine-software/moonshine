<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Traits\WithBadge;
use Throwable;

/**
 * @method static static make(Closure|string $label, Closure|MenuFillerContract|string $filler, string $icon = null, Closure|bool $blank = false)
 */
class MenuItem extends MenuElement
{
    use WithBadge;

    protected string $view = 'moonshine::components.menu.item';

    protected Closure|string|null $url = null;

    protected Closure|bool $blank = false;

    protected ?Closure $whenActive = null;

    protected ActionButtonContract $actionButton;

    final public function __construct(
        Closure|string $label,
        protected Closure|MenuFillerContract|string $filler,
        string $icon = null,
        Closure|bool $blank = false
    ) {
        parent::__construct();

        $this->setLabel($label);

        if ($icon) {
            $this->icon($icon);
        }

        if(is_string($this->filler) && str_contains($this->filler, '\\')) {
            $this->filler = $this->getCore()->getInstances($this->filler);
        }

        if ($this->filler instanceof MenuFillerContract) {
            $this->resolveFiller($this->filler);
        }

        if(is_string($this->filler)) {
            $this->setUrl($this->filler);
        }

        $this->blank($blank);

        $this->actionButton = ActionButton::make($label);
    }

    public function changeButton(Closure $callback): static
    {
        $this->actionButton = $callback($this->actionButton);

        return $this;
    }

    protected function resolveFiller(MenuFillerContract $filler): void
    {
        $this->setUrl(static fn (): string => $filler->getUrl());

        $icon = Attributes::for($filler, Icon::class)->class()->first('icon');

        if (method_exists($filler, 'getBadge')) {
            $this->badge(static fn () => $filler->getBadge());
        }

        if (! is_null($icon) && $this->getIconValue() === '') {
            $this->icon($icon, $this->isCustomIcon(), $this->getIconPath());
        }
    }

    public function getFiller(): MenuFillerContract|Closure|string
    {
        return $this->filler;
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
    public function getUrl(): string
    {
        return value($this->url) ?? '';
    }

    public function blank(Closure|bool $blankCondition = true): static
    {
        $this->blank = value($blankCondition, $this) ?? true;

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

        if ($filler instanceof MenuFillerContract) {
            return $filler->isActive();
        }

        $path = parse_url($this->getUrl(), PHP_URL_PATH) ?? '/';
        $host = parse_url($this->getUrl(), PHP_URL_HOST) ?? '';

        $isActive = function ($path, $host): bool {
            $url = strtok($this->getUrl(), '?');

            if ($path === '/' && $this->getCore()->getRequest()->getHost() === $host) {
                return $this->getCore()->getRequest()->getPath() === $path;
            }

            if ($url === $this->getCore()->getRouter()->getEndpoints()->home()) {
                return $this->getCore()->getRequest()->urlIs($this->getUrl());
            }

            return $this->getCore()->getRequest()->urlIs($host ? "$url*" : "*$url*");
        };

        return is_null($this->whenActive)
            ? $isActive($path, $host)
            : (bool) value($this->whenActive, $path, $host, $this);
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

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
    }

    /**
     * @throws Throwable
     */
    public function viewData(): array
    {
        $viewData = [
            'url' => $this->getUrl(),
        ];

        $viewData['button'] = (string) $this->actionButton
            ->badge($this->hasBadge() ? $this->getBadge() : null)
            ->setUrl($this->getUrl())
            ->customView('moonshine::components.menu.item-link', [
                'url' => $this->getUrl(),
                'label' => $this->getLabel(),
                'icon' => $this->getIcon(6),
                'top' => $this->isTopMode(),
            ]);

        return $viewData;
    }
}
