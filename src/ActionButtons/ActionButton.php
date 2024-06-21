<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Closure;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Contracts\Actions\ActionButtonContract;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Pages\Page;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\AsyncCallback;
use MoonShine\Support\Condition;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\WithBadge;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithModal;
use MoonShine\Traits\WithOffCanvas;
use Throwable;

/**
 * @method static static make(Closure|string $label, Closure|string $url = '', mixed $item = null)
 */
class ActionButton extends MoonShineComponent implements ActionButtonContract
{
    use WithBadge;
    use WithLabel;
    use WithIcon;
    use WithOffCanvas;
    use InDropdownOrLine;
    use WithModal;

    protected bool $isBulk = false;

    protected ?string $bulkForComponent = null;

    protected bool $isAsync = false;

    protected ?string $asyncMethod = null;

    protected ?Closure $onBeforeSetCallback = null;

    protected ?Closure $onAfterSetCallback = null;

    public function __construct(
        Closure|string $label,
        protected Closure|string $url = '#',
        protected mixed $item = null
    ) {
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

    public function getView(): string
    {
        return parent::getView() === ''
            ? 'moonshine::actions.default'
            : parent::getView();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'action' => $this,
        ];
    }

    public function blank(): self
    {
        $this->customAttributes([
            'target' => '_blank',
        ]);

        return $this;
    }

    //TODO 3.0 Make $forComponent argument required
    public function bulk(?string $forComponent = null): self
    {
        $this->isBulk = true;
        $this->bulkForComponent = $forComponent;

        if(is_null($this->modal)) {
            $this->customAttributes(array_filter([
                'data-button-type' => 'bulk-button',
                'data-for-component' => $forComponent,
            ]));
        }

        return $this;
    }

    public function isBulk(): bool
    {
        return $this->isBulk;
    }

    public function bulkForComponent(): ?string
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

    public function getItem(): mixed
    {
        return $this->item;
    }

    public function setItem(mixed $item): self
    {
        if(! is_null($this->onBeforeSetCallback)) {
            $item = value($this->onBeforeSetCallback, $item, $this);
        }

        $this->item = $item;

        value($this->onAfterSetCallback, $item, $this);

        return $this;
    }

    public function onClick(Closure $onClick, ?string $modifier = null): self
    {
        $event = 'x-on:click';

        if (! is_null($modifier)) {
            $event .= ".$modifier";
        }

        $this->customAttributes([
            $event => $onClick($this->getItem()),
        ]);

        return $this;
    }

    public function dispatchEvent(array|string $events): self
    {
        return $this->onClick(
            fn (): string => AlpineJs::dispatchEvents($events),
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
        string|AsyncCallback|null $callback = null,
        ?Page $page = null,
        ?ResourceContract $resource = null
    ): self {
        $this->asyncMethod = $method;

        $this->url = moonshineRouter()->asyncMethodClosure(
            method: $method,
            message: $message,
            params: $params,
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

    public function asyncMethod(): ?string
    {
        return $this->asyncMethod;
    }

    public function async(
        string $method = 'GET',
        ?string $selector = null,
        array $events = [],
        string|AsyncCallback|null $callback = null
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
        ])->onClick(fn (): string => 'request', 'prevent');
    }

    /**
     * @param  array<string, string> $selectors
     */
    public function withParams(array $selectors): self
    {
        $this->customAttributes([
            'data-with-params' => collect($selectors)->map(fn ($value, $key): string => is_numeric($key) ? $value : "$value/$key")->implode(','),
        ]);

        return $this;
    }

    public function purgeAsync(): void
    {
        $this->isAsync = false;

        $removeAsyncAttr = array_merge(
            ['x-data'],
            array_keys(AlpineJs::asyncUrlDataAttributes(
                events: ['events'],
                selector: 'selector',
                callback: 'callback',
            ))
        );

        if($this->attributes->get('x-on:click.prevent') === 'request') {
            $removeAsyncAttr[] = 'x-on:click.prevent';
        }

        foreach ($removeAsyncAttr as $name) {
            $this->removeAttribute($name);
        }
    }

    public function url(mixed $data = null): string
    {
        return value($this->url, $data ?? $this->getItem());
    }

    public function primary(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-primary']);
    }

    public function secondary(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-secondary']);
    }

    public function success(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-success']);
    }

    public function warning(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-warning']);
    }

    public function error(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-error']);
    }
    public function info(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-info']);
    }
}
