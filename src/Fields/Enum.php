<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeEnum;
use UnitEnum;

class Enum extends Select implements DefaultCanBeEnum
{
    /**
     * @param  class-string<UnitEnum>  $class
     * @return $this
     */
    public function attach(string $class): static
    {
        $values = collect($class::cases());

        $this->options(
            $values->mapWithKeys(function ($value) {
                return [
                    $value->name => method_exists($value, 'toString')
                        ? $value->toString()
                        : $value->value,
                ];
            })->toArray()
        );

        return $this;
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if(is_scalar($value)) {
            return $value;
        }

        if(method_exists($value, 'getColor')) {
            $this->badge($value->getColor());
        }

        if(method_exists($value, 'toString')) {
            return $value->toString();
        }

        return $value?->value ?? '';
    }
}
