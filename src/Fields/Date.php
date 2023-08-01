<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Carbon;
use MoonShine\Traits\Fields\DateTrait;

class Date extends Text
{
    use DateTrait;

    protected string $type = 'date';

    public function value(): mixed
    {
        $value = parent::value();

        if (! $value) {
            return $this->isNullable() ? null : '';
        }

        if ($value instanceof Carbon) {
            return $value->format($this->inputFormat);
        }

        return date($this->inputFormat, strtotime((string) $value));
    }

    public function resolvePreview(): string
    {
        $value = parent::resolvePreview();

        return $value !== '' && $value !== '0'
            ? date($this->format, strtotime($value))
            : '';
    }
}
