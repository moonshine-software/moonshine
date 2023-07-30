<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonException;
use MoonShine\Fields\Enum;
use UnitEnum;

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

    public function flattenValues(): array
    {
        return collect($this->values())
            ->mapWithKeys(fn ($value, $key): array => is_array($value) ? $value : [$key => $value])
            ->toArray();
    }

    /**
     * @throws JsonException
     */
    public function isSelected(Model $item, string $value): bool
    {
        $formValue = $this->formViewValue($item);

        if ($this instanceof Enum && $formValue instanceof UnitEnum) {
            $formValue = $formValue->value ?? $formValue->name ?? null;
        }

        if ($this->hasRelationship()) {
            $related = $this->getRelated($item);

            return match (true) {
                $formValue instanceof Collection => $formValue->contains(
                    $related->getKeyName(),
                    '=',
                    $value
                ),
                is_array($formValue) => in_array($value, $formValue),
                default => (string) $formValue === $value
            };
        }

        if ($this->isMultiple()) {
            if (is_string($formValue)) {
                $formValue = json_decode(
                    $formValue,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            }

            return match (true) {
                $formValue instanceof Collection => $formValue->contains(
                    $value
                ),
                is_array($formValue) => in_array($value, $formValue),
                default => (string) $formValue === $value
            };
        }

        return (string) $formValue === $value;
    }
}
