<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\PageContract;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Traits\WithIcon;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\UI\Contracts\Actions\ActionButtonContract;
use MoonShine\UI\Traits\ActionButton\InDropdownOrLine;
use MoonShine\UI\Traits\ActionButton\WithModal;
use MoonShine\UI\Traits\ActionButton\WithOffCanvas;
use MoonShine\UI\Traits\WithBadge;
use Throwable;

/**
 * @method static static make(Closure|string $label, Closure|string $url = '', ?CastedData $data = null)
 */
class ActionButton extends MoonShineComponent implements ActionButtonContract
{
    use WithBadge;
    use WithLabel;
    use WithIcon;
    use WithOffCanvas;
    use InDropdownOrLine;
    use WithModal;

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
        protected ?CastedData $data = null
    ) {
        parent::__construct();

        $this->setLabel($label);
    }

    public function setUrl(Closure|string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public static function emptyHidden(): self
    {
        return self::make('')
            ->customAttributes(['style' => 'display:none']);
    }

    public function blank(): self
    {
        $this->customAttributes([
            'target' => '_blank',
        ]);

        return $this;
    }

    public function bulk(?string $forComponent = null): self
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

    public function onBeforeSet(Closure $onBeforeSet): self
    {
        $this->onBeforeSetCallback = $onBeforeSet;

        return $this;
    }

    public function onAfterSet(Closure $onAfterSet): self
    {
        $this->onAfterSetCallback = $onAfterSet;

        return $this;
    }

    public function getData(): ?CastedData
    {
        return $this->data;
    }

    public function setData(?CastedData $data = null): self
    {
        if(! is_null($this->onBeforeSetCallback)) {
            $data = value($this->onBeforeSetCallback, $data, $this);
        }

        $this->data = $data;

        value($this->onAfterSetCallback, $data, $this);

        return $this;
    }

    public function onClick(Closure $onClick, ?string $modifier = null): self
    {
        $event = 'x-on:click';

        if (! is_null($modifier)) {
            $event .= ".$modifier";
        }

        $this->customAttributes([
            $event => $onClick($this->getData()?->getOriginal()),
        ]);

        return $this;
    }

    public function dispatchEvent(array|string $events): self
    {
        return $this->onClick(
            static fn (): string => AlpineJs::dispatchEvents($events),
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
    ): self {
        $this->asyncMethod = $method;

        $this->url = static fn (mixed $data, ?CastedData $casted): ?string => moonshineRouter()->getEndpoints()->asyncMethod(
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
    ): self {
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
    public function withSelectorsParams(array $selectors): self
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

        if($this->attributes()->get('x-on:click.prevent') === 'request') {
            $removeAsyncAttr[] = 'x-on:click.prevent';
        }

        foreach ($removeAsyncAttr as $name) {
            $this->removeAttribute($name);
        }
    }

    public function getUrl(mixed $data = null): string
    {
        return value($this->url, $data ?? $this->getData()?->getOriginal(), $this->getData());
    }

    public function primary(Closure|bool|null $condition = null): self
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-primary');
    }

    public function secondary(Closure|bool|null $condition = null): self
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-secondary');
    }

    public function success(Closure|bool|null $condition = null): self
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-success');
    }

    public function warning(Closure|bool|null $condition = null): self
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-warning');
    }

    public function info(Closure|bool|null $condition = null): self
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->class('btn-info');
    }

    public function error(Closure|bool|null $condition = null): self
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
            $this,
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
