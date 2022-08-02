<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Support\Collection;

trait CheckboxTrait
{
    public function isChecked(string $value): bool
    {
        if ($this->value() instanceof Collection) {
            return $this->value()->contains("id", "=", $value);
        }

        if (is_array($this->value())) {
            return in_array($value, $this->value());
        }

        return false;
    }
}
