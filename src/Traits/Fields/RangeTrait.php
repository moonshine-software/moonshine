<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait RangeTrait
{
    public string $fromField = 'from';

    public string $toField = 'to';

    protected ?ComponentAttributeBag $fromAttributes = null;

    protected ?ComponentAttributeBag $toAttributes = null;

    public function fromAttributes(array $attributes): static
    {
        $this->fromAttributes = $this->attributes()->merge($attributes);

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
        return $this->reformatAttributes($this->fromAttributes, $this->fromField);
    }

    public function toAttributes(array $attributes): static
    {
        $this->toAttributes = $this->attributes()->merge($attributes);

        return $this;
    }

    public function getToAttributes(): ComponentAttributeBag
    {
        return $this->reformatAttributes($this->toAttributes, $this->toField);
    }

    public function fromTo(string $fromField, string $toField): static
    {
        $this->fromField = $fromField;
        $this->toField = $toField;

        return $this;
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $this->extractFromTo($data);
    }

    protected function extractFromTo(array $data): array
    {
        return [
            $this->fromField => $data[$this->fromField] ?? $this->min,
            $this->toField => $data[$this->toField] ?? $this->max,
        ];
    }

    protected function prepareFill(array $raw = [], mixed $casted = null, int $index = 0): array
    {
        return is_array($raw[$this->column()] ?? false)
            ? $raw[$this->column()]
            : $raw;
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($this->isRawMode()) {
            return "{$value[$this->fromField]} - {$value[$this->toField]}";
        }

        $from = $value[$this->fromField];
        $to = $value[$this->toField];

        if ($this->withStars()) {
            $from = view('moonshine::ui.rating', [
                'value' => $from,
            ])->render();

            $to = view('moonshine::ui.rating', [
                'value' => $to,
            ])->render();
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
}
