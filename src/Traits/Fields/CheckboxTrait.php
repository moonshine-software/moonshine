<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Support\Collection;

trait CheckboxTrait
{
    public function isChecked(string|bool $value): bool
    {
        $formValue = $this->toValue();

        if (is_scalar($formValue)) {
            return $formValue === $value;
        }

        if ($formValue instanceof Collection) {
            return $formValue->isNotEmpty() && $formValue->contains(
                $formValue->first()->getKeyName(),
                '=',
                $value
            );
        }

        if (is_array($formValue)) {
            return in_array($value, $formValue, true);
        }

        return false;
    }
}
