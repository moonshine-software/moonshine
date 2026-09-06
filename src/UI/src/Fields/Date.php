<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use DateTimeInterface;
use MoonShine\Support\Stringify;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Traits\Fields\DateTrait;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;

class Date extends Field implements HasDefaultValueContract, CanBeString, HasUpdateOnPreviewContract
{
    use DateTrait;
    use WithInputExtensions;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;

    protected string $view = 'moonshine::fields.input';

    protected string $type = 'date';

    protected function resolveValue(): mixed
    {
        $value = $this->toValue();

        if (! $value) {
            return $this->isNullable() ? null : '';
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format($this->getInputFormat());
        }

        $timestamp = strtotime(Stringify::value($value));

        return date($this->getInputFormat(), $timestamp === false ? throw new \InvalidArgumentException('Invalid date value.') : $timestamp);
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($value instanceof DateTimeInterface) {
            return $value->format($this->getFormat());
        }

        if (! $value) {
            return '';
        }

        $timestamp = strtotime(Stringify::value($value));

        return date($this->getFormat(), $timestamp === false ? throw new \InvalidArgumentException('Invalid date value.') : $timestamp);
    }

    protected function viewData(): array
    {
        return [
            ...$this->getExtensionsViewData(),
        ];
    }
}
