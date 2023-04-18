<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait SelectTrait
{
    protected array $options = [];

    public function options(array $data): static
    {
        $this->options = $data;

        return $this;
    }

    public function values(): array
    {
        return $this->options;
    }

    public function isSelected(Model $item, string $value): bool
    {
        $formValue = $this->formViewValue($item);

        if (! $formValue) {
            return false;
        }

        if ($this->hasRelationship()) {
            $related = $this->getRelated($item);

            return match (true) {
                $formValue instanceof Collection => $formValue->contains($related->getKeyName(), '=', $value),
                is_array($formValue) => in_array($value, $formValue),
                default => $this->defaultIsSelected($formValue, $value)
            };
        }

        if($this->isMultiple()) {
            if(is_string($formValue)) {
                $formValue = json_decode($formValue, true);
            }

            return match (true) {
                $formValue instanceof Collection => $formValue->contains($value),
                is_array($formValue) => in_array($value, $formValue),
                default => $this->defaultIsSelected($formValue, $value)
            };
        }

        return $this->defaultIsSelected($formValue, $value);
    }

    protected function defaultIsSelected(mixed $formValue, string $value): bool
    {
        return (string) $formValue === $value
            || (! $formValue && (string) $this->getDefault() === $value);
    }
}
