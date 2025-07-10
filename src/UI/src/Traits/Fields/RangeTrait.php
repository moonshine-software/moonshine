<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\VO\FieldEmptyValue;
use MoonShine\UI\Components\Rating;

trait RangeTrait
{
    public string $fromField = 'from';

    public string $toField = 'to';

    protected ?ComponentAttributesBagContract $fromAttributes = null;

    protected ?ComponentAttributesBagContract $toAttributes = null;

    public function getFromField(): string
    {
        return $this->fromField;
    }

    public function getToField(): string
    {
        return $this->toField;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function fromAttributes(array $attributes): static
    {
        $this->fromAttributes = $this->getAttributes()
                ->except(array_keys($attributes))
                ->merge($attributes)
                ->when(
                    $this->fromAttributes,
                    fn (ComponentAttributesBagContract $attributes) => $attributes->merge($this->fromAttributes->getAttributes())
                )
        ;

        return $this;
    }

    protected function reformatAttributes(
        ?ComponentAttributesBagContract $attributes = null,
        string $name = ''
    ): ComponentAttributesBagContract {
        $dataName = $this->getAttribute('data-name');

        return ($attributes ?? $this->getAttributes())
            ->except(['data-name'])
            ->when(
                $dataName,
                static fn (ComponentAttributesBagContract $attr): ComponentAttributesBagContract => $attr->merge([
                    'data-name' => Str::of($dataName)->replaceLast('[]', "[$name]"),
                ])
            );
    }

    public function getFromAttributes(): ComponentAttributesBagContract
    {
        return $this->reformatAttributes($this->fromAttributes, $this->getFromField());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function toAttributes(array $attributes): static
    {
        $this->toAttributes = $this->getAttributes()
            ->except(array_keys($attributes))
            ->merge($attributes)
            ->when(
                $this->toAttributes,
                fn (ComponentAttributesBagContract $attributes) => $attributes->merge($this->toAttributes->getAttributes())
            )
        ;

        return $this;
    }

    public function getToAttributes(): ComponentAttributesBagContract
    {
        return $this->reformatAttributes($this->toAttributes, $this->getToField());
    }

    public function fromTo(string $fromField, string $toField): static
    {
        $this->fromField = $fromField;
        $this->toField = $toField;

        $this->modifyShowFieldRangeName();

        return $this;
    }

    public function getNameDotFrom(): string
    {
        return "{$this->getNameDot()}.{$this->getFromField()}";
    }

    public function getNameDotTo(): string
    {
        return "{$this->getNameDot()}.{$this->getToField()}";
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $this->extractFromTo($data);
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): mixed
    {
        $values = parent::prepareFill($raw, $casted);

        // try to get from array
        if ($values instanceof FieldEmptyValue) {
            $castedValue = $raw[$this->getColumn()] ?? false;
            $values = \is_array($castedValue)
                ? $castedValue
                : $raw;
        }

        if (empty($values[$this->getFromField()]) && empty($values[$this->getToField()])) {
            return new FieldEmptyValue();
        }

        return $values;
    }

    /**
     * @param  array<string, int|string>  $data
     *
     * @return array<string, int|string>
     */
    protected function extractFromTo(array $data): array
    {
        return [
            $this->getFromField() => $data[$this->getFromField()] ?? data_get($this->getDefault(), $this->getFromField(), $this->min),
            $this->getToField() => $data[$this->getToField()] ?? data_get($this->getDefault(), $this->getToField(), $this->max),
        ];
    }

    protected function isNullRange(bool $formatted = false): bool
    {
        $value = $formatted
            ? $this->toFormattedValue()
            : $this->toValue(withDefault: false);

        if (\is_array($value)) {
            return array_filter($value) === [];
        }

        return true;
    }

    protected function resolveRawValue(): mixed
    {
        return $this->resolvePreview();
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($this->isNullRange(formatted: true)) {
            return '';
        }

        $from = $value[$this->getFromField()] ?? $this->min;
        $to = $value[$this->getToField()] ?? $this->max;

        if ($this->isRawMode()) {
            return "$from - $to";
        }

        if ($this->isWithStars()) {
            $from = Rating::make(
                (int) $from
            )->render();

            $to = Rating::make(
                (int) $to
            )->render();
        }

        return "$from - $to";
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $values = $this->getRequestValue();

            if ($values === false) {
                return $item;
            }

            data_set($item, $this->getFromField(), $values[$this->getFromField()] ?? '');
            data_set($item, $this->getToField(), $values[$this->getToField()] ?? '');

            return $item;
        };
    }

    /**
     * @return array<string, string>
     */
    protected function getOnChangeEventAttributes(?string $url = null): array
    {
        if ($url) {
            $this->fromAttributes(
                AlpineJs::onChangeSaveField(
                    $url,
                    $this->getFromField(),
                )
            );

            $this->toAttributes(
                AlpineJs::onChangeSaveField(
                    $url,
                    $this->getToField(),
                )
            );
        }

        return [];
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this
            ->fromAttributes([
                'name' => $this->getNameAttribute($this->getFromField()),
                'data-sync-with' => $this->getNameAttribute($this->getToField()),
            ])
            ->toAttributes([
                'name' => $this->getNameAttribute($this->getToField()),
                'data-sync-with' => $this->getNameAttribute($this->getFromField()),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        /**
         * @var Collection<string, mixed> $errors
         */
        $errors = new Collection(parent::getErrors());

        return [
            ...$errors,
            $this->getNameDot() => [
                ...(data_get($errors->undot()->toArray(), $this->getNameDotFrom()) ?? []),
                ...(data_get($errors->undot()->toArray(), $this->getNameDotTo()) ?? []),
            ],
        ];
    }

    protected function resolveValue(): mixed
    {
        if ($this->isNullRange()) {
            return [
                $this->getFromField() => null,
                $this->getToField() => null,
            ];
        }

        return $this->toValue();
    }

    protected function resolveValidationErrorClasses(): void
    {
        if (Arr::has($this->getErrors(), $this->getNameDotFrom())) {
            $this->fromAttributes(['class' => 'form-invalid']);
        }

        if (Arr::has($this->getErrors(), $this->getNameDotTo())) {
            $this->toAttributes(['class' => 'form-invalid']);
        }
    }
}
