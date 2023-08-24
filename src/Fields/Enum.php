<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeEnum;

class Enum extends Select implements DefaultCanBeEnum
{
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

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if(method_exists($value, 'getColor')) {
            $this->badge($value->getColor());
        }

        return data_get($this->values(), $value?->value, '');
    }
}
