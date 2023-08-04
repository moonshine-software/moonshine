<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Traits\Fields\SlideTrait;

class SlideField extends Number implements HasValueExtraction, DefaultCanBeArray
{
    use SlideTrait;

    protected string $view = 'moonshine::fields.slide';

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

    public function extractOnFill(): bool
    {
        return true;
    }

    public function extractValues(array $data): array
    {
        return [
            $this->fromField => $data[$this->fromField] ?? $this->min,
            $this->toField => $data[$this->toField] ?? $this->max,
        ];
    }

    protected function resolveOnSave(): ?Closure
    {
        return function ($item) {
            $values = $this->requestValue();

            if ($values === false) {
                return $item;
            }

            $item->{$this->fromField} = $values[$this->fromField] ?? '';
            $item->{$this->toField} = $values[$this->toField] ?? '';

            return $item;
        };
    }
}
