<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Core\Collections\Components;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Traits\ActionButton\InDropdownOrLine;
use MoonShine\UI\Traits\ActionButton\WithModal;
use MoonShine\UI\Traits\ActionButton\WithOffCanvas;
use MoonShine\UI\Traits\Components\WithComponents;
use MoonShine\UI\Traits\WithBadge;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Throwable;

/**
 * @method static static make(Closure|string $label, Closure|string $url = '', ?DataWrapperContract $data = null)
 */
class ActionButton extends MoonShineComponent implements ActionButtonContract, HasComponentsContract
{
    use WithBadge;
    use WithLabel;
    use WithIcon;
    use WithOffCanvas;
    use InDropdownOrLine;
    use WithModal;
    use WithComponents;

    protected string $view = 'moonshine::components.action-button';

    protected bool $isBulk = false;

    protected ?string $bulkForComponent = null;

    protected bool $isAsync = false;

    protected ?string $asyncMethod = null;

    protected ?Closure $onBeforeSetCallback = null;

    protected ?Closure $onAfterSetCallback = null;

    public function __construct(
        Closure|string $label,
        protected Closure|string $url = '#',
        protected ?DataWrapperContract $data = null
    ) {
        parent::__construct();

        $this->setLabel($label);
    }

    public function setUrl(Closure|string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public static function emptyHidden(): static
    {
        return self::make('')
            ->customAttributes(['style' => 'display:none']);
    }

    public function blank(): static
    {
        $this->customAttributes([
            'target' => '_blank',
        ]);

        return $this;
    }

    public function bulk(?string $forComponent = null): static
    {
        $this->isBulk = true;
        $this->bulkForComponent = $forComponent;

        if(is_null($this->modal)) {
            $this->customAttributes(array_filter([
                'data-button-type' => 'bulk-button',
                'data-for-component' => $this->getBulkForComponent(),
            ]));
        }

        return $this;
    }

    public function isBulk(): bool
    {
        return $this->isBulk;
    }

    public function getBulkForComponent(): ?string
    {
        return $this->bulkForComponent;
    }

    public function onBeforeSet(Closure $onBeforeSet): static
    {
        $this->onBeforeSetCallback = $onBeforeSet;

        return $this;
    }

    public function onAfterSet(Closure $onAfterSet): static
    {
        $this->onAfterSetCallback = $onAfterSet;

        return $this;
    }

    public function getData(): ?DataWrapperContract
    {
        return $this->data;
    }

    public function setData(?DataWrapperContract $data = null): static
    {
        if(! is_null($this->onBeforeSetCallback)) {
            $data = value($this->onBeforeSetCallback, $data, $this);
        }

        $this->data = $data;

        value($this->onAfterSetCallback, $data, $this);

        return $this;
    }

    public function onClick(Closure $onClick, ?string $modifier = null): static
    {
        $event = 'x-on:click';

        if (! is_null($modifier)) {
            $event .= ".$modifier";
        }

        $this->customAttributes([
            $event => $onClick($this->getData()?->getOriginal(), $this),
        ]);

        return $this;
    }

    public function dispatchEvent(array|string $events): static
    {
        if(! $this->getAttributes()->has('x-data')) {
            $this->xDataMethod('actionButton');
        }

        return $this->onClick(
            static fn (): string => "dispatchEvents(
                 `" . AlpineJs::prepareEvents($events) . "`,
                 `_component_name,_token,_method`
             )",
            'prevent'
        );
    }

    /**
     * @throws Throwable
     */
    public function method(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        ?string $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): static {
        $this->asyncMethod = $method;

        $this->url = fn (mixed $data, ?DataWrapperContract $casted): ?string => $this->getCore()->getRouter()->getEndpoints()->method(
            method: $method,
            message: $message,
            params: array_filter([
                'resourceItem' => $casted?->getKey(),
                ...value($params, $casted?->getOriginal()),
            ], static fn ($value) => filled($value)),
            page: $page,
            resource: $resource,
        );

        return $this->async(
            selector: $selector,
            events: $events,
            callback: $callback
        );
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isAsyncMethod(): bool
    {
        return ! is_null($this->asyncMethod);
    }

    public function getAsyncMethod(): ?string
    {
        return $this->asyncMethod;
    }

    public function disableAsync(): static
    {
        $this->isAsync = false;

        return $this;
    }

    public function async(
        HttpMethod $method = HttpMethod::GET,
        ?string $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null
    ): static {
        $this->isAsync = true;

        return $this->customAttributes([
            'x-data' => 'actionButton',
            ...AlpineJs::asyncUrlDataAttributes(
                method: $method,
                events: $events,
                selector: $selector,
                callback: $callback,
            ),
        ])->onClick(static fn (): string => 'request', 'prevent');
    }

    /**
     * @param  array<string, string> $selectors
     */
    public function withSelectorsParams(array $selectors): static
    {
        return $this->customAttributes(
            AlpineJs::asyncSelectorsParamsAttributes($selectors)
        );
    }

    public function hasComponent(): bool
    {
        return $this->isInOffCanvas() || $this->isInModal();
    }

    public function getComponent(): ?MoonShineComponent
    {
        if($this->isInModal()) {
            return $this->getModal();
        }

        if($this->isInOffCanvas()) {
            return $this->getOffCanvas();
        }

        return null;
    }

    public function setComponents(iterable $components): static
    {
        return $this;
    }

    public function hasComponents(): bool
    {
        return $this->hasComponent();
    }

    protected function prepareComponents(): Components
    {
        return Components::make($this->hasComponents() ? [$this->getComponent()] : []);
    }

    public function purgeAsyncTap(): bool
    {
        return tap($this->isAsync(), fn () => $this->purgeAsync());
    }

    /*
     * In this case, the form inside the modal works in async mode,
     * so the async mode is removed from the button.
     */
    public function purgeAsync(): void
    {
        $this->isAsync = false;

        $removeAsyncAttr = array_merge(
            ['x-data'],
            array_keys(AlpineJs::asyncUrlDataAttributes(
                events: ['events'],
                selector: 'selector',
            ))
        );

        if($this->getAttributes()->get('x-on:click.prevent') === 'request') {
            $removeAsyncAttr[] = 'x-on:click.prevent';
        }

        foreach ($removeAsyncAttr as $name) {
            $this->removeAttribute($name);
        }
    }

    public function getUrl(mixed $data = null): string
    {
        return value($this->url, $data ?? $this->getData()?->getOriginal(), $this->getData(), $this);
    }

    public function primary(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-primary');
    }

    public function secondary(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-secondary');
    }

    public function success(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-success');
    }

    public function warning(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-warning');
    }

    public function info(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-info');
    }

    public function error(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-error');
    }

    protected function isSeeParams(): array
    {
        return [
            $this->getData()?->getOriginal(),
            $this->getData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'inDropdown' => $this->isInDropdown(),
            'hasComponent' => $this->hasComponent(),
            'component' => $this->hasComponent() ? $this->getComponent() : '',
            'label' => $this->getLabel(),
            'url' => $this->getUrl(),
            'icon' => $this->getIcon(4),
            'badge' => $this->hasBadge() ? $this->getBadge() : false,
        ];
    }
}
