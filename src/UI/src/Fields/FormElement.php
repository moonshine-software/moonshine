<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Core\Traits\NowOn;
use MoonShine\Core\TypeCasts\MixedDataWrapper;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\VO\FieldEmptyValue;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\Applies;
use MoonShine\UI\Traits\Fields\ShowWhen;
use MoonShine\UI\Traits\Fields\WithQuickFormElementAttributes;
use MoonShine\UI\Traits\WithLabel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class FormElement extends MoonShineComponent implements HasAssetsContract
{
    use WithLabel;
    use ShowWhen;
    use Applies;
    use NowOn;
    use WithQuickFormElementAttributes;

    protected array $propertyAttributes = ['type'];

    protected ?FormElement $parent = null;

    protected ?string $formName = null;

    protected string $column;

    protected ?string $virtualColumn = null;

    protected mixed $value = null;

    protected mixed $resolvedValue = null;

    protected bool $isValueResolved = false;

    protected bool $resolveValueOnce = false;

    protected mixed $rawValue = null;

    protected ?Closure $rawValueCallback = null;

    protected mixed $formattedValue = null;

    protected ?Closure $formattedValueCallback = null;

    protected ?Closure $fromRaw = null;

    protected ?Closure $fillCallback = null;

    protected ?Closure $afterFillCallback = null;

    protected mixed $data = null;

    protected int $rowIndex = 0;

    protected static ?Closure $requestValueResolver = null;

    protected ?string $requestKeyPrefix = null;

    protected bool $hasOld = true;

    protected MoonShineComponentAttributeBag $wrapperAttributes;

    /**
     * @param  (Closure(static $ctx): string)|string|null  $label
     * @param  ?string  $column
     * @param  ?Closure(mixed $original, int $index, static $ctx): mixed  $formatted
     */
    public function __construct(
        Closure|string|null $label = null,
        ?string $column = null,
        ?Closure $formatted = null
    ) {
        parent::__construct();

        $this->attributes = new MoonShineComponentAttributeBag(
            $this->getPropertyAttributes()->toArray()
        );

        $this->wrapperAttributes = new MoonShineComponentAttributeBag();

        $this->setLabel($label ?? $this->getLabel());
        $this->setColumn(
            trim($column ?? str($this->getLabel())->lower()->snake()->value())
        );

        if (! is_null($formatted)) {
            $this->setFormattedValueCallback($formatted);
        }
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

    public function formName(?string $formName = null): static
    {
        $this->formName = $formName;

        return $this;
    }

    public function getFormName(): ?string
    {
        return $this->formName;
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

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): static
    {
        if ($this->showWhenState) {
            foreach (array_keys($this->showWhenCondition) as $key) {
                $this->showWhenCondition[$key]['showField'] = $column;
            }
        }

        $this->column = $column;

        return $this;
    }

    public function virtualColumn(string $column): static
    {
        $this->virtualColumn = $column;

        return $this;
    }

    public function getVirtualColumn(): string
    {
        return $this->virtualColumn ?? $this->getColumn();
    }

    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null): mixed
    {
        if ($this->isFillChanged()) {
            return value(
                $this->fillCallback,
                is_null($casted) ? $raw : $casted->getOriginal(),
                $this
            );
        }

        $default = new FieldEmptyValue();

        $value = data_get(is_null($casted) ? $raw : $casted->getOriginal(), $this->getColumn(), $default);

        if (is_null($value) || $value === false || $value instanceof FieldEmptyValue) {
            $value = data_get($raw, $this->getColumn(), $default);
        }

        return $value;
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $data;
    }

    protected function resolveFill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): static
    {
        $this->setData($casted);
        $this->setRowIndex($index);

        $value = $this->prepareFill($raw, $casted);

        if ($value instanceof FieldEmptyValue) {
            return $this;
        }

        $this->setRawValue($value);

        $value = $this->reformatFilledValue($value);

        $this->setValue($value);

        if (! is_null($this->afterFillCallback)) {
            return value($this->afterFillCallback, $this);
        }

        return $this;
    }

    public function fillData(mixed $value, int $index = 0): static
    {
        $casted = $value instanceof DataWrapperContract
            ? $value
            : new MixedDataWrapper($value);

        return $this->resolveFill(
            $casted->toArray(),
            $casted,
            $index
        );
    }

    public function fillCast(mixed $value, ?DataCasterContract $cast = null, int $index = 0): static
    {
        $casted = $cast ? $cast->cast($value) : new MixedDataWrapper($value);

        return $this->fillData($casted, $index);
    }

    public function fill(mixed $value = null, ?DataWrapperContract $casted = null, int $index = 0): static
    {
        return $this->resolveFill([
            $this->getColumn() => $value,
        ], $casted, $index);
    }

    public function toRawValue(): mixed
    {
        if ($this->isRawValueModified()) {
            return value($this->rawValueCallback, $this->rawValue, $this->getData()?->getOriginal(), $this);
        }

        return $this->resolveRawValue();
    }

    protected function resolveRawValue(): mixed
    {
        return $this->rawValue;
    }

    protected function setRawValue(mixed $value = null): static
    {
        $this->rawValue = $value;

        return $this;
    }

    public function setValue(mixed $value = null): static
    {
        $this->value = $value;

        return $this->setRawValue($value);
    }

    protected function setData(?DataWrapperContract $data = null): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?DataWrapperContract
    {
        return $this->data;
    }

    public function toValue(bool $withDefault = true): mixed
    {
        $default = $withDefault && $this instanceof HasDefaultValueContract
            ? $this->getDefault()
            : null;

        return $this->isBlankValue() ? $default : $this->value;
    }

    public function getValue(bool $withOld = true): mixed
    {
        if ($this->isValueResolved && $this->resolveValueOnce) {
            return $this->resolvedValue;
        }

        if (! $this->hasOld) {
            $withOld = false;
        }

        $empty = new FieldEmptyValue();
        $old = $withOld
            ? $this->getCore()->getRequest()->getOld($this->getNameDot(), $empty)
            : $empty;

        if ($withOld && $old !== $empty) {
            return $old;
        }

        $this->isValueResolved = true;

        return $this->resolvedValue = $this->resolveValue();
    }

    protected function resolveValue(): mixed
    {
        return $this->toValue();
    }

    protected function isBlankValue(): bool
    {
        return is_null($this->value);
    }

    protected function setFormattedValue(mixed $value = null): static
    {
        $this->formattedValue = $value;

        return $this;
    }

    protected function setFormattedValueCallback(Closure $formattedValueCallback): void
    {
        $this->formattedValueCallback = $formattedValueCallback;
    }

    public function getFormattedValueCallback(): ?Closure
    {
        return $this->formattedValueCallback;
    }

    public function toFormattedValue(): mixed
    {
        if (! is_null($this->getFormattedValueCallback())) {
            $this->setFormattedValue(
                value(
                    $this->getFormattedValueCallback(),
                    $this->getData()?->getOriginal(),
                    $this->getRowIndex(),
                    $this
                )
            );
        }

        return $this->formattedValue ?? $this->toValue(withDefault: false);
    }

    protected function setRowIndex(int $index = 0): static
    {
        $this->rowIndex = $index;

        return $this;
    }

    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    /**
     * @param  Closure(mixed $data, static $field): mixed  $callback
     */
    public function changeFill(Closure $callback): static
    {
        $this->fillCallback = $callback;

        return $this;
    }

    /**
     * @param  Closure(static $ctx): static  $callback
     */
    public function afterFill(Closure $callback): static
    {
        $this->afterFillCallback = $callback;

        return $this;
    }

    public function isFillChanged(): bool
    {
        return ! is_null($this->fillCallback);
    }

    public function isRawValueModified(): bool
    {
        return ! is_null($this->rawValueCallback);
    }

    /**
     * @param  Closure(mixed $raw, mixed $original, static): mixed  $callback
     *
     * @return $this
     */
    public function modifyRawValue(Closure $callback): static
    {
        $this->rawValueCallback = $callback;

        return $this;
    }

    /**
     * @param  Closure(mixed $raw, static): mixed  $callback
     *
     * @return $this
     */
    public function fromRaw(Closure $callback): static
    {
        $this->fromRaw = $callback;

        return $this;
    }

    public function getValueFromRaw(mixed $raw): mixed
    {
        if (is_null($this->fromRaw)) {
            return $raw;
        }

        return value($this->fromRaw, $raw, $this);
    }

    public function getDefaultIfExists(): mixed
    {
        return $this instanceof HasDefaultValueContract
            ? $this->getDefault()
            : false;
    }

    public function reset(): static
    {
        return $this
            ->setValue()
            ->setRawValue()
            ->setFormattedValue();
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

    public function getRequestKeyPrefix(): ?string
    {
        return $this->requestKeyPrefix;
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
            'label' => $this->getLabel(),
            'column' => $this->getColumn(),
            'value' => $this->getValue(),
            'errors' => data_get($this->getErrors(), $this->getNameDot()),
        ];
    }
}
