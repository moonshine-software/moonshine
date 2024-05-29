<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Carbon;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\Contracts\HasReactivity;
use MoonShine\Traits\Fields\DateTrait;
use MoonShine\Traits\Fields\HasPlaceholder;
use MoonShine\Traits\Fields\Reactivity;
use MoonShine\Traits\Fields\UpdateOnPreview;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithInputExtensions;

class Date extends Field implements HasDefaultValue, DefaultCanBeString, HasUpdateOnPreview, HasReactivity
{
    use DateTrait;
    use WithInputExtensions;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;
    use Reactivity;

    protected string $view = 'moonshine::fields.input';

    protected string $type = 'date';

    protected function prepareRequestValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return e($value);
        }

        return $value;
    }

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
