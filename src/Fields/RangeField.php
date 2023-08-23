<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Traits\Fields\RangeTrait;

class RangeField extends Number implements DefaultCanBeArray
{
    use RangeTrait;

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;

    public function dates(): self
    {
        $this->type = 'date';

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
        $value = $this->value();

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
