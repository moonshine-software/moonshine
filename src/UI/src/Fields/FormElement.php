<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Core\Traits\NowOn;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\WithQuickFormElementAttributes;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class FormElement extends MoonShineComponent implements HasAssetsContract
{
    public $propertyAttributes;
    use NowOn;
    use WithQuickFormElementAttributes;

    protected ?FormElement $parent = null;

    protected bool $isGroup = false;

    protected bool $withWrapper = true;

    protected ?string $requestKeyPrefix = null;

    protected ?string $formName = null;

    protected MoonShineComponentAttributeBag $wrapperAttributes;

    protected ?Closure $onChangeUrl = null;

    protected static ?Closure $requestValueResolver = null;

    public function __construct()
    {
        parent::__construct();

        $this->attributes = new MoonShineComponentAttributeBag(
            $this->getPropertyAttributes()->toArray()
        );

        $this->wrapperAttributes = new MoonShineComponentAttributeBag();
    }

    protected function getPropertyAttributes(): Collection
    {
        return collect($this->propertyAttributes)->mapWithKeys(
            function ($attr): array {
                $property = (string) str($attr)->camel();

                return isset($this->{$property})
                    ? [$attr => $this->{$property}]
                    : [];
            }
        );
    }

    public function getIdentity(string $index = null): string
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

    public function horizontal(): static
    {
        $this->customWrapperAttributes([
            'class' => 'form-group-inline',
        ]);

        return $this;
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

    public function getWrapperAttributes(): MoonShineComponentAttributeBag
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
                static fn ($str) => $str->prepend("$prefix.")
            )->value()
        );

        return $this;
    }

    public function hasRequestValue(string|int|null $index = null): bool
    {
        return $this->getCore()->getRequest()->has($this->getRequestNameDot($index));
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @param  Closure(string|int|null $index, mixed $default, static $ctx): mixed  $resolver
     */
    public static function requestValueResolver(Closure $resolver): void
    {
        static::$requestValueResolver = $resolver;
    }

    public function getRequestValue(string|int|null $index = null): mixed
    {
        if (! is_null(static::$requestValueResolver)) {
            return value(static::$requestValueResolver, $index, $this->getDefaultIfExists(), $this);
        }

        return $this->prepareRequestValue(
            $this->getCore()->getRequest()->get(
                $this->getRequestNameDot($index),
                $this->getDefaultIfExists()
            ) ?? false
        );
    }

    public function getRequestNameDot(string|int|null $index = null): string
    {
        return str($this->getNameDot())
            ->when(
                $this->getRequestKeyPrefix(),
                fn (Stringable $str): Stringable => $str->prepend(
                    "{$this->getRequestKeyPrefix()}."
                )
            )
            ->when(
                ! is_null($index) && $index !== '',
                static fn (Stringable $str): Stringable => $str->append(".$index")
            )->value();
    }

    public function getDotNestedToName(string $value): string
    {
        if (! str_contains($value, '.')) {
            return $value;
        }

        return str($value)->explode('.')
            ->map(static fn ($part, $index): string => $index === 0 ? $part : "[$part]")
            ->implode('');
    }

    public function getDefaultIfExists(): mixed
    {
        return $this instanceof HasDefaultValueContract
            ? $this->getDefault()
            : false;
    }

    public function getRequestKeyPrefix(): ?string
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
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
    ): static {
        $url = static fn (?DataWrapperContract $data): ?string => $this->getCore()->getRouter()->getEndpoints()->method(
            method: $method,
            message: $message,
            params: array_filter([
                'resourceItem' => $data?->getKey(),
                ...value($params, $data?->getOriginal()),
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
     *
     * @return $this
     */
    public function onChangeUrl(
        Closure $url,
        HttpMethod $method = HttpMethod::GET,
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
        HttpMethod $method = HttpMethod::GET,
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

    protected function getOnChangeEventAttributes(?string $url = null): array
    {
        return $url ? AlpineJs::requestWithFieldValue($url, $this->getColumn()) : [];
    }

    protected function isOnChangeCondition(): bool
    {
        return true;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function resolveValidationErrorClasses(): void
    {
        $this->class([
            'form-invalid' => Arr::has($this->getErrors(), $this->getNameDot()),
        ]);
    }

    public function getErrors(): array
    {
        return $this->getCore()->getRequest()->getFormErrors($this->getFormName());
    }

    protected function resolveAssets(): void
    {
        if (! $this->isConsoleMode() && ! $this->isPreviewMode()) {
            $this->getAssetManager()->add($this->getAssets());
        }
    }

    protected function shouldUseAssets(): bool
    {
        return ! $this->isPreviewMode();
    }

    protected function systemViewData(): array
    {
        return [
            'attributes' => $this->getAttributes(),
            'errors' => data_get($this->getErrors(), $this->getNameDot()),
        ];
    }
}
