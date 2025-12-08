<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Attribute;
use Closure;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\WithBadgeContract;
use MoonShine\MenuManager\Attributes\Badge;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Traits\WithBadge;
use Throwable;

/**
 * @method static static make(Closure|MenuFillerContract|string $filler, Closure|string $label = null, string $icon = null, Closure|bool $blank = false)
 */
class MenuItem extends MenuElement implements WithBadgeContract
{
    use WithBadge;

    protected string $view = 'moonshine::components.menu.item';

    /**
     * @var string|null|(Closure():MenuFillerContract|string)
     */
    protected Closure|string|null $url = null;

    /**
     * @var  bool|(Closure($this): bool)
     */
    protected Closure|bool $blank = false;

    protected ?Closure $whenActive = null;

    protected ActionButtonContract $actionButton;

    /**
     * @param  Closure|MenuFillerContract|class-string<MenuFillerContract>  $filler
     */
    final public function __construct(
        protected Closure|MenuFillerContract|string $filler,
        Closure|string|null $label = null,
        ?string $icon = null,
        Closure|bool $blank = false
    ) {
        parent::__construct();

        if ($label !== null) {
            $this->setLabel($label);
        }

        if ($icon) {
            $this->icon($icon);
        }

        if (\is_string($this->filler) && str_contains($this->filler, '\\') && ($filler = $this->getCore()->getInstances($this->filler)) !== null) {
            $this->filler = $filler;
        }

        if ($this->filler instanceof MenuFillerContract) {
            $this->resolveFiller($this->filler);
        }

        if (\is_string($this->filler)) {
            $this->setUrl($this->filler);
        }

        if ($this->filler instanceof Closure) {
            $this->setUrl($this->filler);
        }

        $this->blank($blank);

        $this->actionButton = ActionButton::make($this->getLabel());
    }

    /**
     * @param  Closure(ActionButtonContract): ActionButtonContract  $callback
     *
     * @return $this
     */
    public function changeButton(Closure $callback): static
    {
        $this->actionButton = $callback($this->actionButton->customAttributes(['@mouseenter' => false]));

        return $this;
    }

    protected function resolveFiller(MenuFillerContract $filler): void
    {
        $this->setUrl(static fn (): string => $filler->getUrl());

        if (! $this->hasLabel()) {
            $this->setLabel($filler->getTitle());
        }

        $icon = $this->getCore()->getAttributes()->get(
            /** @phpstan-ignore-next-line  */
            default: fn (): ?string => Attributes::for($filler, Icon::class)->first('icon'),
            target: $filler::class,
            attribute: Icon::class,
            column: [0 => 'icon']
        ) ?? $filler->getIcon();

        $badge = $this->getCore()->getAttributes()->get(
            /** @phpstan-ignore-next-line  */
            fn () => Attributes::for($filler, Badge::class)->first()?->value,
            $filler::class,
            Badge::class,
            type: Attribute::TARGET_CLASS,
            column: [0 => 'value']
        ) ?? $filler->getBadge();

        if ($badge !== null) {
            $this->badge(static fn (): mixed => $badge);
        }

        if (! \is_null($icon) && $this->getIconValue() === '') {
            $this->icon($icon, $this->isCustomIcon(), $this->getIconPath());
        }
    }

    public function getFiller(): MenuFillerContract|Closure|string
    {
        return $this->filler;
    }

    /**
     * @param Closure(string $path, string $host, static $ctx): bool  $when
     */
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
        $url = $this->url instanceof Closure ? \call_user_func($this->url) : $this->url;

        if (\is_null($url)) {
            $url = '';
        }

        if ($url instanceof MenuFillerContract) {
            return $url->getUrl();
        }

        return $url;
    }

    /**
     * @param  (Closure($this): bool)|bool  $blankCondition
     */
    public function blank(Closure|bool $blankCondition = true): static
    {
        $this->blank = value($blankCondition, $this);

        return $this;
    }

    public function isBlank(): bool
    {
        return value($this->blank, $this);
    }

    /**
     * @throws Throwable
     */
    public function isActive(): bool
    {
        $filler = $this->getFiller();

        if ($filler instanceof MenuFillerContract && \is_null($this->whenActive)) {
            return $filler->isActive();
        }

        $path = parse_url($this->getUrl(), PHP_URL_PATH) ?? '/';
        $host = parse_url($this->getUrl(), PHP_URL_HOST) ?? '';

        $isActive = function ($path, $host): bool {
            $url = strtok($this->getUrl(), '?');

            if ($url === false) {
                return false;
            }

            if ($path === '/' && $this->getCore()->getRequest()->getHost() === $host) {
                return $this->getCore()->getRequest()->getPath() === $path;
            }

            if ($url === $this->getCore()->getRouter()->getEndpoints()->home()) {
                return $this->getCore()->getRequest()->urlIs($this->getUrl());
            }

            if ($url === '/' || trim($url) === '') {
                return $this->getCore()->getRequest()->urlIs($host ? $url : "*$url");
            }

            return $this->getCore()->getRequest()->urlIs($host ? "$url*" : "*$url*");
        };

        return \is_null($this->whenActive)
            ? $isActive($path, $host)
            : (bool) value($this->whenActive, $path, $host, $this);
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        if ($this->isBlank()) {
            $this->actionButton = $this->actionButton->customAttributes([
                'target' => '_blank',
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
    protected function viewData(): array
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
                'onlyIcon' => $this->isOnlyIcon(),
                'icon' => $this->getIcon(),
                'top' => $this->isTopMode(),
            ]);

        return $viewData;
    }
}
