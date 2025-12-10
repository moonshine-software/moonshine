<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Core\Collections\Components;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Traits\ActionButton\InDropdownOrLine;
use MoonShine\UI\Traits\ActionButton\WithModal;
use MoonShine\UI\Traits\ActionButton\WithOffCanvas;
use MoonShine\UI\Traits\Components\WithComponents;
use MoonShine\UI\Traits\Components\WithSlotContent;
use MoonShine\UI\Traits\WithBadge;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @template TData of mixed = mixed
 * @template TWrapper of DataWrapperContract<TData> = DataWrapperContract
 *
 * @method static static make(Closure|string $label = '', Closure|string $url = 'javascript:void(0);', ?DataWrapperContract $data = null)
 *
 * @implements ActionButtonContract<TData, TWrapper, Modal, OffCanvas>
 */
class ActionButton extends MoonShineComponent implements
    ActionButtonContract,
    HasComponentsContract
{
    use WithBadge;
    use WithLabel;
    use WithIcon;
    use WithOffCanvas;
    use WithModal;
    use InDropdownOrLine;
    use WithComponents;
    use WithSlotContent;

    protected string $view = 'moonshine::components.action-button';

    protected bool $isBulk = false;

    protected ?string $bulkForComponent = null;

    protected bool $isAsync = false;

    protected bool $raw = false;

    protected ?HttpMethod $asyncHttpMethod = null;

    protected ?AsyncCallback $asyncCallback = null;

    /**
     * @var string[]|null
     */
    protected ?array $asyncEvents = null;

    /**
     * @var string|string[]|null
     */
    protected null|string|array $asyncSelector = null;

    protected ?string $asyncMethod = null;

    protected ?Closure $onBeforeSetCallback = null;

    protected ?Closure $onAfterSetCallback = null;

    public function __construct(
        Closure|string $label = '',
        protected Closure|string $url = 'javascript:void(0);',
        /** @param null|TWrapper $data */
        protected ?DataWrapperContract $data = null,
    ) {
        parent::__construct();

        $this->setLabel($label);
    }

    /**
     * @param  (Closure(TData $original, null|TWrapper $casted, static $ctx): string)|string  $url
     */
    public function setUrl(Closure|string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public static function emptyHidden(): static
    {
        return self::make()->style('display:none');
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

        if (\is_null($this->modal)) {
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

    /**
     * @param  (Closure(null|TWrapper $data, ActionButtonContract $ctx): ?TWrapper)  $onBeforeSet
     */
    public function onBeforeSet(Closure $onBeforeSet): static
    {
        $this->onBeforeSetCallback = $onBeforeSet;

        return $this;
    }

    /**
     * @param  Closure(null|TWrapper $data, ActionButtonContract $ctx): void  $onAfterSet
     */
    public function onAfterSet(Closure $onAfterSet): static
    {
        $this->onAfterSetCallback = $onAfterSet;

        return $this;
    }

    /**
     * @return null|TWrapper
     */
    public function getData(): ?DataWrapperContract
    {
        return $this->data;
    }

    /**
     * @param  null|TWrapper  $data
     *
     */
    public function setData(?DataWrapperContract $data = null): static
    {
        if (! \is_null($this->onBeforeSetCallback)) {
            $data = \call_user_func($this->onBeforeSetCallback, $data, $this);
        }

        $this->data = $data;

        if (! \is_null($this->onAfterSetCallback)) {
            \call_user_func($this->onAfterSetCallback, $data, $this);
        }

        return $this;
    }

    /**
     * @param  Closure(ActionButtonContract $ctx): string  $onClick
     */
    public function onClick(Closure $onClick, ?string $modifier = null): static
    {
        $event = 'x-on:click';

        if (! \is_null($modifier)) {
            $event .= ".$modifier";
        }

        $this->customAttributes([
            $event => $onClick($this),
        ]);

        return $this;
    }

    /**
     * @param  string[]|string  $events
     * @param  string[]  $exclude
     */
    public function dispatchEvent(array|string $events, array $exclude = [], bool $withoutPayload = false): static
    {
        if (! $this->getAttributes()->has('x-data')) {
            $this->xDataMethod('actionButton');
        }

        $excludes = $withoutPayload ? '*' : implode(',', [
            ...$exclude,
            '_component_name',
            '_token',
            '_method',
        ]);

        return $this->onClick(
            static fn (): string => "dispatchEvents(
                 `" . AlpineJs::prepareEvents($events) . "`,
                 `$excludes`
             )",
            'prevent',
        );
    }

    /**
     * @param non-empty-array<string> $keys
     */
    public function hotKeys(array $keys, bool $withBadge = false): static
    {
        $hotKeys = implode(',', $keys);
        $badge = $withBadge ? Str::of($hotKeys)
            ->explode(',')
            ->map(function (string $key): string {
                $key = trim($key);
                $map = [
                    'meta' => '⌘',
                    'shift' => '⇧',
                    'control' => '^',
                    'alt' => '⌥',
                    'backspace' => '⌫',
                    'enter' => '⏎',
                    'escape' => 'esc',
                ];

                return ucfirst($map[$key] ?? $key);
            })
            ->implode('+') : null;

        return $this->customAttributes([
            'x-data' => 'actionButton',
            'data-hot-keys' => $hotKeys,
        ])->badge($badge);
    }

    public function download(): static
    {
        return $this->customAttributes([
            'download' => true,
            'data-async-response-type' => 'blob',
        ]);
    }

    public function withoutLoading(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this->removeAttribute('data-without-loading');
        }

        return $this->customAttributes([
            'data-without-loading' => true,
        ]);
    }

    /**
     * @param array<string, mixed>|(Closure(TData $original): array<string, mixed>) $params = []
     * @param null|string|string[] $selector
     * @param string[] $events
     */
    public function method(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        null|string|array $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
    ): static {
        $this->asyncMethod = $method;

        $this->url = fn (mixed $data, ?DataWrapperContract $casted): string => $this->getCore()->getRouter()->getEndpoints()->method(
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
            callback: $callback,
        );
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isAsyncMethod(): bool
    {
        return ! \is_null($this->asyncMethod);
    }

    public function getAsyncMethod(): ?string
    {
        return $this->asyncMethod;
    }

    public function disableAsync(): static
    {
        $this->purgeAsync();

        return $this;
    }

    /**
     * @param  null|string|string[]  $selector
     * @param  string[]  $events
     */
    public function async(
        HttpMethod $method = HttpMethod::GET,
        null|string|array $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
    ): static {
        $this->isAsync = true;
        $this->asyncHttpMethod = $method;
        $this->asyncSelector = $selector;
        $this->asyncEvents = $events;
        $this->asyncCallback = $callback;

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
            AlpineJs::asyncSelectorsParamsAttributes($selectors),
        );
    }

    public function withQueryParams(): static
    {
        return $this->customAttributes(
            AlpineJs::asyncWithQueryParamsAttributes(),
        );
    }

    public function hasComponent(): bool
    {
        return $this->isInOffCanvas() || $this->isInModal();
    }

    public function getComponent(): ?ComponentContract
    {
        if ($this->isInModal()) {
            return $this->getModal();
        }

        if ($this->isInOffCanvas()) {
            return $this->getOffCanvas();
        }

        return null;
    }

    /**
     * Blocked because only two components inside are allowed (Modal and OffCanvas)
     *
     * @param  iterable<array-key, ComponentContract>  $components
     */
    public function setComponents(iterable $components): static
    {
        return $this;
    }

    public function hasComponents(): bool
    {
        return $this->hasComponent();
    }

    public function getComponents(): ComponentsContract
    {
        return $this->getPreparedComponents();
    }

    protected function prepareComponents(): Components
    {
        return Components::make($this->hasComponents() ? [$this->getComponent()] : []);
    }

    protected function purgeAsyncTap(): bool
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
        $this->asyncSelector = null;
        $this->asyncHttpMethod = null;
        $this->asyncEvents = null;
        $this->asyncCallback = null;

        $removeAsyncAttr = array_merge(
            ['x-data'],
            array_keys(AlpineJs::asyncUrlDataAttributes(
                events: ['events'],
                selector: 'selector',
            )),
        );

        if ($this->getAttribute('x-on:click.prevent') === 'request') {
            $removeAsyncAttr[] = 'x-on:click.prevent';
        }

        foreach ($removeAsyncAttr as $name) {
            $this->removeAttribute($name);
        }
    }

    /**
     * @param  TData  $data
     *
     */
    public function getUrl(mixed $data = null): string
    {
        return value($this->url, $data ?? $this->getData()?->getOriginal(), $this->getData(), $this);
    }

    public function rawMode(): self
    {
        $this->raw = true;

        return $this;
    }

    public function isRaw(): bool
    {
        return $this->raw;
    }

    public function primary(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->removeClasses()->class('btn-primary');
    }

    public function secondary(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->removeClasses()->class('btn-secondary');
    }

    public function success(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->removeClasses()->class('btn-success');
    }

    public function warning(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->removeClasses()->class('btn-warning');
    }

    public function info(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->removeClasses()->class('btn-info');
    }

    public function error(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->removeClasses()->class('btn-error');
    }

    public function square(Closure|bool|null $condition = null): static
    {
        if (! (value($condition, $this) ?? true)) {
            return $this;
        }

        return $this->removeClasses()->class('btn-square');
    }

    private function removeClasses(): static
    {
        return $this->removeClass('btn-(primary|secondary|info|success|warning|error)');
    }

    /**
     * @return array<array-key, TData|TWrapper|null>
     */
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
            'icon' => $this->getIcon(),
            'badge' => $this->hasBadge() ? $this->getBadge() : false,
            'raw' => $this->isRaw(),
            'slot' => $this->getSlot(),
        ];
    }
}
