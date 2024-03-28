<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Pages\Page;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\AsyncCallback;
use MoonShine\Support\Condition;
use MoonShine\Traits\Fields\WithFormElementAttributes;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithView;

abstract class FormElement implements MoonShineRenderable, HasAssets, CanBeEscapedWhenCastToString
{
    use Makeable;
    use WithFormElementAttributes;
    use WithComponentAttributes;
    use WithView;
    use WithAssets;
    use HasCanSee;
    use Conditionable;

    protected ?FormElement $parent = null;

    protected bool $isGroup = false;

    protected bool $withWrapper = true;

    protected ?string $requestKeyPrefix = null;

    protected ?string $formName = null;

    protected array $wrapperAttributes = [];

    protected ?Closure $beforeRender = null;

    protected ?Closure $afterRender = null;

    protected ?Closure $onChangeUrl = null;

    protected ?Closure $requestValueResolver = null;

    private View|string|null $cachedRender = null;

    public function parent(): ?FormElement
    {
        return $this->parent;
    }

    public function hasParent(): bool
    {
        return ! is_null($this->parent);
    }

    public function setParent(FormElement $field): static
    {
        $this->parent = $field;

        return $this;
    }

    protected function group(): static
    {
        $this->isGroup = true;

        return $this;
    }

    public function isGroup(): bool
    {
        return $this->isGroup;
    }

    public function withoutWrapper(mixed $condition = null): static
    {
        $this->withWrapper = Condition::boolean($condition, false);

        return $this;
    }

    public function hasWrapper(): bool
    {
        return $this->withWrapper;
    }

    public function customWrapperAttributes(array $attributes): static
    {
        $this->wrapperAttributes = [...$attributes, ...$this->wrapperAttributes];

        return $this;
    }

    public function wrapperAttributes(): ComponentAttributeBag
    {
        return new ComponentAttributeBag(
            $this->wrapperAttributes
        );
    }

    public function setRequestKeyPrefix(?string $key): static
    {
        $this->requestKeyPrefix = $key;

        return $this;
    }

    public function appendRequestKeyPrefix(string $value, ?string $prefix = null): static
    {
        $this->setRequestKeyPrefix(
            str($value)->when(
                $prefix,
                fn ($str) => $str->prepend("$prefix.")
            )->value()
        );

        return $this;
    }

    public function hasRequestValue(string|int|null $index = null): bool
    {
        return request()->has($this->requestNameDot($index));
    }

    public function requestValueResolver(Closure $resolver): static
    {
        $this->requestValueResolver = $resolver;

        return $this;
    }

    public function requestValue(string|int|null $index = null): mixed
    {
        if (! is_null($this->requestValueResolver)) {
            return value(
                $this->requestValueResolver,
                $this->requestNameDot($index),
                $this->defaultIfExists(),
                $this,
            ) ?? false;
        }

        return request($this->requestNameDot($index), $this->defaultIfExists()) ?? false;
    }

    protected function requestNameDot(string|int|null $index = null): string
    {
        return str($this->nameDot())
            ->when(
                $this->requestKeyPrefix(),
                fn (Stringable $str): Stringable => $str->prepend(
                    "{$this->requestKeyPrefix()}."
                )
            )
            ->when(
                ! is_null($index) && $index !== '',
                fn (Stringable $str): Stringable => $str->append(".$index")
            )->value();
    }

    protected function dotNestedToName(string $value): string
    {
        if (! str_contains($value, '.')) {
            return $value;
        }

        return str($value)->explode('.')
            ->map(fn ($part, $index) => $index === 0 ? $part : "[$part]")
            ->implode('');
    }

    public function defaultIfExists(): mixed
    {
        return $this instanceof HasDefaultValue
            ? $this->getDefault()
            : false;
    }

    public function requestKeyPrefix(): ?string
    {
        return $this->requestKeyPrefix;
    }

    public function formName(?string $formName = null): static
    {
        $this->formName = $formName;

        return $this;
    }

    public function getFormName(): ?string
    {
        return $this->formName;
    }

    public function beforeRender(Closure $closure): static
    {
        $this->beforeRender = $closure;

        return $this;
    }

    public function afterRender(Closure $closure): static
    {
        $this->afterRender = $closure;

        return $this;
    }

    public function getBeforeRender(): View|string
    {
        return is_null($this->beforeRender)
            ? ''
            : value($this->beforeRender, $this);
    }

    public function getAfterRender(): View|string
    {
        return is_null($this->afterRender)
            ? ''
            : value($this->afterRender, $this);
    }

    public function onChangeMethod(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        ?string $selector = null,
        array $events = [],
        string|AsyncCallback|null $callback = null,
        ?Page $page = null,
        ?ResourceContract $resource = null,
    ): static {
        $url = moonshineRouter()->asyncMethodClosure(
            method: $method,
            message: $message,
            params: $params,
            page: $page,
            resource: $resource,
        );

        return $this->onChangeUrl(
            url: $url,
            events: $events,
            selector: $selector,
            callback: $callback
        );
    }

    public function onChangeUrl(
        Closure $url,
        string $method = 'PUT',
        array $events = [],
        ?string $selector = null,
        string|AsyncCallback|null $callback = null,
    ): static {
        $this->onChangeUrl = $url;

        return $this->onChangeAttributes(
            method: $method,
            events: $events,
            selector: $selector,
            callback: $callback
        );
    }

    protected function onChangeAttributes(
        string $method = 'GET',
        array $events = [],
        ?string $selector = null,
        string|AsyncCallback|null $callback = null
    ): static {
        return $this->customAttributes(
            AlpineJs::asyncUrlDataAttributes(
                method: $method,
                events: $events,
                selector: $selector,
                callback: $callback,
            )
        );
    }

    protected function onChangeEventAttributes(?string $url = null): array
    {
        return $url ? AlpineJs::requestWithFieldValue($url, $this->column()) : [];
    }

    protected function onChangeCondition(): bool
    {
        return true;
    }

    protected function viewData(): array
    {
        return [];
    }

    public function render(): View|Closure|string
    {
        if (! is_null($this->cachedRender)) {
            return $this->cachedRender;
        }

        if ($this->getAssets()) {
            moonshineAssets()->add($this->getAssets());
        }

        if (! is_null($this->onChangeUrl) && $this->onChangeCondition()) {
            $onChangeUrl = value($this->onChangeUrl, $this->getData(), $this->toValue(), $this);

            $this->customAttributes(
                $this->onChangeEventAttributes($onChangeUrl),
            );
        }

        if ($this->getView() === '') {
            return $this->toValue();
        }

        return $this->cachedRender = view(
            $this->getView(),
            $this->toArray()
        );
    }

    public function toArray(): array
    {
        return [
            'element' => $this,
            'value' => $this->value(),
            ...$this->viewData(),
        ];
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }

    public function escapeWhenCastingToString($escape = true): self
    {
        return $this;
    }
}
