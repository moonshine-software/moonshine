<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait CheckboxTrait
{
    public function isChecked(Model $item, string|bool $value): bool
    {
        $formValue = $this->formViewValue($item);

        if (is_scalar($formValue)) {
            return $formValue === $value;
        }

        if ($formValue instanceof Collection) {
            return $formValue->isNotEmpty()
                ? $formValue->contains($formValue->first()->getKeyName(), '=', $value)
                : false;
        }

        if (is_array($formValue)) {
            return in_array($value, $formValue, true);
        }

        return false;
    }
}
