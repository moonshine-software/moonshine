<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeEnum;
use UnitEnum;

class Enum extends Select implements DefaultCanBeEnum
{
    public function indexViewValue(Model $item, bool $container = true): string
    {
        $value = $item->{$this->field()};

        if (isset($this->values()[$value?->value])) {
            return (string) ($this->values()[$value->value] ?? '');
        }

        return parent::indexViewValue($item, $container);
    }

    public function attach(string $class): static
    {
        /* @var UnitEnum $class ; */
        $this->options(array_column($class::cases(), 'name', 'value'));

        return $this;
    }
}
