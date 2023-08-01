<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeEnum;

class Enum extends Select implements DefaultCanBeEnum
{
    public function resolvePreview(): string
    {
        $value = $this->value();

        if (isset($this->values()[$value?->value])) {
            return (string) ($this->values()[$value->value] ?? '');
        }

        return parent::resolvePreview();
    }

    public function attach(string $class): static
    {
        /* @var UnitEnum $class ; */
        $this->options(
            array_column(
                $class::cases(),
                'name',
                'value'
            )
        );

        return $this;
    }
}
