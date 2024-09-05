<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Support\Carbon;
use MoonShine\Contracts\UI\HasReactivityContract;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Traits\Fields\DateTrait;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;

class Date extends Field implements HasDefaultValueContract, CanBeString, HasUpdateOnPreviewContract, HasReactivityContract
{
    use DateTrait;
    use WithInputExtensions;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;
    use Reactivity;

    protected string $view = 'moonshine::fields.input';

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

    protected function viewData(): array
    {
        return [
            ...$this->getExtensionsViewData(),
        ];
    }
}
