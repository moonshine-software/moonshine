<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Components\Rating;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\FieldEmptyValue;

trait RangeTrait
{
    public string $fromField = 'from';

    public string $toField = 'to';

    protected ?ComponentAttributeBag $fromAttributes = null;

    protected ?ComponentAttributeBag $toAttributes = null;

    public function fromAttributes(array $attributes): static
    {
        $this->fromAttributes = $this->attributes()
            ->except(array_keys($attributes))
            ->merge($attributes);

        return $this;
    }

    protected function reformatAttributes(
        ?ComponentAttributeBag $attributes = null,
        string $name = ''
    ): ComponentAttributeBag {
        $dataName = $this->attributes()->get('data-name');

        return ($attributes ?? $this->attributes())
            ->except(['data-name'])
            ->when(
                $dataName,
                fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                    'data-name' => str($dataName)->replaceLast('[]', "[$name]"),
                ])
            );
    }

    public function getFromAttributes(): ComponentAttributeBag
    {
        return $this->reformatAttributes($this->fromAttributes, $this->fromField)
            ->class([
                'form-invalid' => formErrors(session('errors', false), $this->getFormName())
                    ->has("{$this->nameDot()}.$this->fromField"),
            ]);
    }

    public function toAttributes(array $attributes): static
    {
        $this->toAttributes = $this->attributes()
            ->except(array_keys($attributes))
            ->merge($attributes);

        return $this;
    }

    public function getToAttributes(): ComponentAttributeBag
    {
        return $this->reformatAttributes($this->toAttributes, $this->toField)
            ->class([
                'form-invalid' => formErrors(session('errors', false), $this->getFormName())
                    ->has($this->getNameDotTo()),
            ]);
    }

    public function fromTo(string $fromField, string $toField): static
    {
        $this->fromField = $fromField;
        $this->toField = $toField;

        return $this;
    }

    public function getNameDotFrom(): string
    {
        return "{$this->nameDot()}.$this->fromField";
    }

    public function getNameDotTo(): string
    {
        return "{$this->nameDot()}.$this->toField";
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $this->extractFromTo($data);
    }

    protected function prepareFill(array $raw = [], mixed $casted = null, int $index = 0): mixed
    {
        $values = parent::prepareFill($raw, $casted);

        // try to get from array
        if($values instanceof FieldEmptyValue) {
            $castedValue = $raw[$this->column()] ?? false;
            $values = is_array($castedValue)
                ? $castedValue
                : $raw;
        }

        if(empty($values[$this->fromField]) && empty($values[$this->toField])) {
            return new FieldEmptyValue();
        }

        return $values;
    }

    protected function extractFromTo(array $data): array
    {
        return [
            $this->fromField => $data[$this->fromField] ?? data_get($this->getDefault(), $this->fromField, $this->min),
            $this->toField => $data[$this->toField] ?? data_get($this->getDefault(), $this->toField, $this->max),
        ];
    }

    protected function isNullRange(bool $formatted = false): bool
    {
        $value = $formatted
            ? $this->toFormattedValue()
            : $this->toValue(withDefault: false);

        if (is_array($value)) {
            return array_filter($value) === [];
        }

        return true;
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($this->isNullRange(formatted: true)) {
            return '';
        }

        $from = $value[$this->fromField] ?? $this->min;
        $to = $value[$this->toField] ?? $this->max;

        if ($this->isRawMode()) {
            return "$from - $to";
        }

        if ($this->withStars()) {
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
            $values = $this->requestValue();

            if ($values === false) {
                return $item;
            }

            data_set($item, $this->fromField, $values[$this->fromField] ?? '');
            data_set($item, $this->toField, $values[$this->toField] ?? '');

            return $item;
        };
    }

    protected function onChangeEventAttributes(?string $url = null): array
    {
        if($url) {
            $this->fromAttributes(
                AlpineJs::requestWithFieldValue(
                    $url,
                    $this->fromField,
                )
            );

            $this->toAttributes(
                AlpineJs::requestWithFieldValue(
                    $url,
                    $this->toField,
                )
            );
        }

        return [];
    }
}
