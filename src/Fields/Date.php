<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Carbon;
use MoonShine\Traits\Fields\DateTrait;

class Date extends Text
{
    use DateTrait;

    protected string $type = 'date';

    protected function resolveValue(): mixed
    {
        $value = $this->toValue();

        if (! $value) {
            return $this->isNullable() ? null : '';
        }

        if ($value instanceof Carbon) {
            return $value->format($this->getInputFormat());
        }

        return date($this->getInputFormat(), strtotime((string) $value));
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($value instanceof Carbon) {
            return $value->format($this->getFormat());
        }

        return $value
            ? date($this->getFormat(), strtotime((string) $value))
            : '';
    }
}
