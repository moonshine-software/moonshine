<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class Enum extends Select
{
    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        $value = $item->{$this->field()};

        if (isset($this->values()[$value->value])) {
            return $this->values()[$value->value];
        }

        return parent::indexViewValue($item, $container);
    }

    public function attach(string $class): static
    {
        /* @var UnitEnum $class ; */
        $this->options(array_column($class::cases(), 'name', 'value'));

        return $this;
    }

    public function isSelected(Model $item, string $value): bool
    {
        $formValue = $this->formViewValue($item);

        if (!$formValue) {
            return false;
        }

        if (is_string($formValue)) {
            return $formValue === $value || (string) $this->getDefault() === $value;
        }

        return (string) $formValue->value === $value
            || (!$formValue->value && (string) $this->getDefault() === $value);
    }
}
