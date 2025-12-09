<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\FormElementContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Core\Traits\NowOn;
use MoonShine\Core\TypeCasts\MixedDataWrapper;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\VO\FieldEmptyValue;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\WrapperWithApplyContract;
use MoonShine\UI\Traits\Fields\Applies;
use MoonShine\UI\Traits\Fields\ShowWhen;
use MoonShine\UI\Traits\Fields\WithQuickFormElementAttributes;
use MoonShine\UI\Traits\WithLabel;
use WeakReference;

abstract class FormElement extends MoonShineComponent implements FormElementContract
{
    use WithLabel;
    use ShowWhen;
    use Applies;
    use NowOn;
    use WithQuickFormElementAttributes;

    /**
     * @var string[]
     */
    protected array $propertyAttributes = ['type'];

    /**
     * @var WeakReference<FormElementContract>|null
     */
    protected ?WeakReference $parent = null;

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

    /** @var null|Closure(mixed, int, static): mixed */
    protected ?Closure $formattedValueCallback = null;

    protected ?Closure $fromRaw = null;

    protected ?Closure $fillCallback = null;

    /** @var null|Closure(static): static */
    protected ?Closure $afterFillCallback = null;

    protected mixed $data = null;

    protected int $rowIndex = 0;

    protected static ?Closure $requestValueResolver = null;

    protected ?Closure $onRequestValue = null;

    protected ?string $requestKeyPrefix = null;

    protected bool $hasOld = true;

    protected bool $isOldValue = false;

    protected bool $isGroup = false;

    protected ComponentAttributesBagContract $wrapperAttributes;

    /**
     * @param  (Closure(static $ctx): string)|string|null  $label
     * @param  ?Closure(mixed $original, int $index, static $ctx): mixed  $formatted
     */
    public function __construct(
        Closure|string|null $label = null,
        ?string $column = null,
        ?Closure $formatted = null,
    ) {
        parent::__construct();

        $this->attributes = new MoonShineComponentAttributeBag(
            $this->getPropertyAttributes()->toArray(),
        );

        $this->wrapperAttributes = new MoonShineComponentAttributeBag();

        $this->setLabel($label ?? $this->getLabel());
        $this->setColumn(
            trim($column ?? Str::of($this->getLabel())->lower()->snake()->value()),
        );

        if (! \is_null($formatted)) {
            $this->setFormattedValueCallback($formatted);
        }
    }

    /**
     * @return Collection<string, mixed>
     */
    protected function getPropertyAttributes(): Collection
    {
        /** @var Collection<array-key, string> $collection */
        $collection = new Collection($this->propertyAttributes);

        return $collection->mapWithKeys(
            function (string $attr): array {
                $property = (string) Str::of($attr)->camel();

                return isset($this->{$property})
                    ? [$attr => $this->{$property}]
                    : [];
            },
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

    public function getParent(): ?FormElementContract
    {
        return $this->parent?->get();
    }

    public function hasParent(): bool
    {
        return ! \is_null($this->parent) && $this->parent->get() instanceof FormElementContract;
    }

    public function setParent(FormElementContract $field): static
    {
        $this->parent = WeakReference::create($field);

        return $this;
    }

    public function getIdentity(?string $index = null): string
    {
        return (string) Str::of($this->getNameAttribute($index))
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

    /**
     * @param  array<string, mixed>  $raw
     */
    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null): mixed
    {
        if ($this->isFillChanged()) {
            return \call_user_func(
                $this->fillCallback,
                \is_null($casted) ? $raw : $casted->getOriginal(),
                $this,
            );
        }

        $default = new FieldEmptyValue();

        $value = data_get(\is_null($casted) ? $raw : $casted->getOriginal(), $this->getColumn(), $default);

        if (\is_null($value) || $value === false || $value instanceof FieldEmptyValue) {
            $value = data_get($raw, $this->getColumn(), $default);
        }

        return $value;
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $raw
     */
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

        if (! \is_null($this->afterFillCallback)) {
            return \call_user_func($this->afterFillCallback, $this);
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
            $index,
        );
    }

    public function fillCast(mixed $value, ?DataCasterContract $cast = null, int $index = 0): static
    {
        if ($value instanceof DataWrapperContract) {
            return $this->fillData($value, $index);
        }

        $casted = $cast ? $cast->cast($value) : new MixedDataWrapper($value);

        return $this->fillData($casted, $index);
    }

    public function fill(mixed $value = null, ?DataWrapperContract $casted = null, int $index = 0): static
    {
        $wrapper = $this instanceof WrapperWithApplyContract || $this instanceof FieldsWrapperContract;
        $empty = [];

        return $this->resolveFill(
            $wrapper
                ? $value
                : data_set($empty, $this->getColumn(), $value),
            $casted,
            $index
        );
    }

    public function toRawValue(): mixed
    {
        if ($this->isRawValueModified()) {
            return \call_user_func($this->rawValueCallback, $this->rawValue, $this->getData()?->getOriginal(), $this);
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
            $this->isOldValue = true;

            $this->setValue(
                $this->resolveOldValue($old),
            );
        }

        $this->isValueResolved = true;

        return $this->resolvedValue = $this->resolveValue();
    }

    protected function resolveOldValue(mixed $old): mixed
    {
        return $old;
    }

    public function isOldValue(): bool
    {
        return $this->isOldValue;
    }

    protected function resolveValue(): mixed
    {
        return $this->toValue();
    }

    protected function isBlankValue(): bool
    {
        return \is_null($this->value);
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

    /** @return null|Closure(mixed $original, int $index, static $ctx): mixed  */
    public function getFormattedValueCallback(): ?Closure
    {
        return $this->formattedValueCallback;
    }

    public function toFormattedValue(): mixed
    {
        if (! \is_null($this->getFormattedValueCallback())) {
            $this->setFormattedValue(
                \call_user_func(
                    $this->getFormattedValueCallback(),
                    $this->getData()?->getOriginal(),
                    $this->getRowIndex(),
                    $this,
                ),
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
        return ! \is_null($this->fillCallback);
    }

    public function isRawValueModified(): bool
    {
        return ! \is_null($this->rawValueCallback);
    }

    /**
     * @param  Closure(mixed $raw, mixed $original, static): mixed  $callback
     */
    public function modifyRawValue(Closure $callback): static
    {
        $this->rawValueCallback = $callback;

        return $this;
    }

    /**
     * @param  Closure(mixed $raw, static): mixed  $callback
     */
    public function fromRaw(Closure $callback): static
    {
        $this->fromRaw = $callback;

        return $this;
    }

    public function getValueFromRaw(mixed $raw): mixed
    {
        if (\is_null($this->fromRaw)) {
            return $raw;
        }

        return \call_user_func($this->fromRaw, $raw, $this);
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

    public function removeWrapperAttribute(string $name): static
    {
        $this->getWrapperAttributes()->remove($name);

        return $this;
    }

    /**
     * @param  string|array<int|string, bool|string>  $classes
     */
    public function wrapperClass(string|array $classes): static
    {
        $this->wrapperAttributes = $this->wrapperAttributes->class($classes);

        return $this;
    }

    public function removeWrapperClass(string $pattern): static
    {
        $before = $this->wrapperAttributes->get('class', '');

        $this->getWrapperAttributes()->remove('class');

        $this->wrapperAttributes = $this->wrapperAttributes->class(
            trim((string) preg_replace("/(?<=\s|^)$pattern(?=\s|$)/", '', (string) $before))
        );

        return $this;
    }

    /**
     * @param  string|array<int|string, bool|string>  $styles
     */
    public function wrapperStyle(string|array $styles): static
    {
        $this->wrapperAttributes = $this->wrapperAttributes->style($styles);

        return $this;
    }

    public function getWrapperAttributes(): ComponentAttributesBagContract
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
            Str::of($value)->when(
                $prefix,
                static fn ($str) => $str->prepend("$prefix."),
            )->value(),
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

    /**
     * @param  Closure(mixed $value, string $name, mixed $default, static $ctx): mixed  $callback
     */
    public function onRequestValue(Closure $callback): static
    {
        $this->onRequestValue = $callback;

        return $this;
    }

    public function getRequestValue(string|int|null $index = null): mixed
    {
        if (! \is_null(static::$requestValueResolver)) {
            return \call_user_func(static::$requestValueResolver, $index, $this->getDefaultIfExists(), $this);
        }

        $value = $this->prepareRequestValue(
            $this->getCore()->getRequest()->get(
                $this->getRequestNameDot($index),
                $this->getDefaultIfExists(),
            ) ?? false,
        );

        if ($this->onRequestValue instanceof Closure) {
            return \call_user_func(
                $this->onRequestValue,
                $value,
                $this->getRequestNameDot($index),
                $this->getDefaultIfExists(),
                $this
            );
        }

        return $value;
    }

    public function getRequestNameDot(string|int|null $index = null): string
    {
        return Str::of($this->getNameDot())
            ->when(
                $this->getRequestKeyPrefix(),
                fn (Stringable $str): Stringable => $str->prepend(
                    "{$this->getRequestKeyPrefix()}.",
                ),
            )
            ->when(
                ! \is_null($index) && $index !== '',
                static fn (Stringable $str): Stringable => $str->append(".$index"),
            )->value();
    }

    public function getDotNestedToName(string $value): string
    {
        if (! str_contains($value, '.')) {
            return $value;
        }

        return Str::of($value)->explode('.')
            ->map(static fn ($part, $index): string => $index === 0 ? $part : "[$part]")
            ->implode('');
    }

    public function getRequestKeyPrefix(): ?string
    {
        return $this->requestKeyPrefix;
    }

    protected function resolveValidationErrorClasses(): void
    {
        $this->class([
            'form-invalid' => Arr::has($this->getErrors(), $this->getNameDot()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->getCore()->getRequest()->getFormErrors($this->getFormName());
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

    /**
     * @return array<string, mixed>
     */
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

    public function flushState(): void
    {
        $this->flushRenderCache();
        $this->resolvedValue = null;
    }

    protected function onClone(): void
    {
        if ($this instanceof HasFieldsContract) {
            foreach ($this->getFields()->onlyFields() as $field) {
                $field->flushState();
            }
        }
    }
}
