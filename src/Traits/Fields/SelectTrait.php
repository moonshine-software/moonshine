<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Support\Collection;
use JsonException;
use MoonShine\Fields\Enum;
use UnitEnum;

trait SelectTrait
{
    protected array $options = [];

    public function options(Closure|array $data): static
    {
        $this->options = is_callable($data)
            ? $data($this)
            : $data;

        return $this;
    }

    public function values(): array
    {
        return $this->options;
    }

    /**
     * @throws JsonException
     */
    public function isSelected(string $value): bool
    {
        $formValue = $this->value();

        if ($this instanceof Enum && $formValue instanceof UnitEnum) {
            $formValue = $formValue->value ?? $formValue->name ?? null;
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

    public function flattenValues(): array
    {
        return collect($this->values())
            ->mapWithKeys(fn ($value, $key): array => is_array($value) ? $value : [$key => $value])
            ->toArray();
    }
}
