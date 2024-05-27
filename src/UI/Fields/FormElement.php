<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\ViewErrorBag;
use MoonShine\Core\Contracts\Fields\HasAssets;
use MoonShine\Core\Contracts\Fields\HasDefaultValue;
use MoonShine\Core\Contracts\Resources\ResourceContract;
use MoonShine\Core\Pages\Page;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Traits\HasCanSee;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\NowOn;
use MoonShine\Support\Traits\WithAssets;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Traits\Components\WithComponentAttributes;
use MoonShine\UI\Traits\Fields\WithQuickFormElementAttributes;
use MoonShine\UI\Traits\WithViewRenderer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class FormElement extends MoonShineComponent implements HasAssets
{
    use Makeable;
    use NowOn;
    use WithQuickFormElementAttributes;
    use WithComponentAttributes;
    use WithViewRenderer;
    use WithAssets;
    use HasCanSee;
    use Conditionable;

    protected ?FormElement $parent = null;

    protected bool $isGroup = false;

    protected bool $withWrapper = true;

    protected ?string $requestKeyPrefix = null;

    protected ?string $formName = null;

    protected MoonShineComponentAttributeBag $wrapperAttributes;

    protected ?Closure $onChangeUrl = null;

    protected ?Closure $beforeRender = null;

    protected ?Closure $afterRender = null;

    public function __construct()
    {
        parent::__construct();

        $this->wrapperAttributes = new MoonShineComponentAttributeBag();
    }

    public function identity(string $index = null): string
    {
        return (string) str($this->getNameAttribute($index))
            ->replace(['[', ']'], '_')
            ->replaceMatches('/\${index\d+}/', '')
            ->replaceMatches('/_{2,}/', '_')
            ->trim('_')
            ->snake()
            ->slug('_');
    }

    public function getParent(): ?FormElement
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
        $this->withWrapper = value($condition, $this) ?? false;

        return $this;
    }

    public function hasWrapper(): bool
    {
        return $this->withWrapper;
    }

    public function customWrapperAttributes(array $attributes): static
    {
        $this->wrapperAttributes = $this->wrapperAttributes->merge($attributes);

        return $this;
    }

    public function wrapperAttributes(): MoonShineComponentAttributeBag
    {
        return $this->wrapperAttributes;
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
        return moonshine()->getRequest($this->requestNameDot($index), $this) !== $this;
    }

    public function requestValue(string|int|null $index = null): mixed
    {
        return moonshine()->getRequest(
            $this->requestNameDot($index),
            $this->defaultIfExists()
        ) ?? false;
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

    public function onChangeMethod(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        ?string $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?Page $page = null,
        ?ResourceContract $resource = null,
    ): static {
        $url = static fn (mixed $item): ?string => moonshineRouter()->asyncMethod(
            method: $method,
            message: $message,
            params: array_filter([
                'resourceItem' => $item instanceof Model ? $item->getKey() : null,
                ...value($params, $item),
            ], static fn ($value) => filled($value)),
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

    /**
     * @param  Closure(mixed $data, mixed $value, self $field): string  $url
     * @param  string[]  $events
     * @return $this
     */
    public function onChangeUrl(
        Closure $url,
        string $method = 'PUT',
        array $events = [],
        ?string $selector = null,
        ?AsyncCallback $callback = null,
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
        ?AsyncCallback $callback = null
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
        return $url ? AlpineJs::requestWithFieldValue($url, $this->getColumn()) : [];
    }

    protected function onChangeCondition(): bool
    {
        return true;
    }

    public function beforeRender(Closure $closure): static
    {
        $this->beforeRender = $closure;

        return $this;
    }

    public function getBeforeRender(): View|string
    {
        return is_null($this->beforeRender)
            ? ''
            : value($this->beforeRender, $this);
    }

    public function afterRender(Closure $closure): static
    {
        $this->afterRender = $closure;

        return $this;
    }

    public function getAfterRender(): View|string
    {
        return is_null($this->afterRender)
            ? ''
            : value($this->afterRender, $this);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function prepareBeforeRender(): void
    {
        if (! is_null($this->onChangeUrl) && $this->onChangeCondition()) {
            $onChangeUrl = value($this->onChangeUrl, $this->getData(), $this->toValue(), $this);

            $this->customAttributes(
                $this->onChangeEventAttributes($onChangeUrl),
            );
        }

        if (! $this->isPreviewMode()) {
            $id = $this->attributes->get('id');

            $this->customAttributes([
                $id ? 'id' : ':id' => $id ?? "\$id(`field`)",
                'name' => $this->getNameAttribute(),
            ]);

            $this->resolveValidationErrorClasses();
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function resolveValidationErrorClasses(): void
    {
        $this->class([
            'form-invalid' => formErrors($this->getErrors(), $this->getFormName())
                ->has($this->getNameAttribute()),
        ]);
    }

    public function getErrors(): ViewErrorBag
    {
        // TODO @isolate
        return session()->get('errors', new ViewErrorBag());
    }

    protected function resolveAssets(): void
    {
        if (! $this->isPreviewMode()) {
            moonshineAssets()->add($this->getAssets());
        }
    }

    protected function resolveRender(): View|Closure|string
    {
        if ($this->isPreviewMode()) {
            return $this->preview();
        }

        if ($this->getView() === '') {
            return $this->toValue();
        }

        return $this->renderView();
    }

    protected function systemViewData(): array
    {
        return [
            'attributes' => $this->attributes(),
            'errors' => $this->getErrors(),
        ];
    }
}
