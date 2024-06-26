<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\PageContract;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\Traits\NowOn;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Traits\HasCanSee;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithAssets;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Contracts\Fields\HasAssets;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
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

    public static ?Closure $errors = null;

    public function __construct()
    {
        parent::__construct();

        $this->resolveAssets();

        $this->wrapperAttributes = new MoonShineComponentAttributeBag();
    }

    public static function errorsResolver(Closure $callback): void
    {
        self::$errors = $callback;
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
        return moonshine()->getRequest()->has($this->getRequestNameDot($index));
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        return $value;
    }

    public function getRequestValue(string|int|null $index = null): mixed
    {
        return $this->prepareRequestValue(
            moonshine()->getRequest()->get(
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
            ->map(static fn ($part, $index) => $index === 0 ? $part : "[$part]")
            ->implode('');
    }

    public function getDefaultIfExists(): mixed
    {
        return $this instanceof HasDefaultValue
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
        $url = static fn (?CastedData $data): ?string => moonshineRouter()->getEndpoints()->asyncMethod(
            method: $method,
            message: $message,
            params: array_filter([
                'resourceItem' => $data->getKey(),
                ...value($params, $data->getOriginal()),
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
        if (! is_null($this->onChangeUrl) && $this->isOnChangeCondition()) {
            $onChangeUrl = value($this->onChangeUrl, $this->getData(), $this->toValue(), $this);

            $this->customAttributes(
                $this->getOnChangeEventAttributes($onChangeUrl),
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
            'form-invalid' => Arr::has($this->getErrors(), $this->getNameDot()),
        ]);
    }

    public static function resolveErrors(?string $formName, object $context): mixed
    {
        return value(self::$errors, $formName, $context) ?? [];
    }

    public function getErrors(): mixed
    {
        return self::resolveErrors($this->getFormName(), $this) ?? [];
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
            'attributes' => $this->getAttributes(),
            'errors' => data_get($this->getErrors(), $this->getNameDot()),
        ];
    }
}
