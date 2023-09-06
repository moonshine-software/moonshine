<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Illuminate\Support\Collection;
use JsonException;
use UnitEnum;

final class SelectOptions
{
    /**
     * @throws JsonException
     */
    public static function isSelected(mixed $current, string $value): bool
    {
        if ($current instanceof UnitEnum) {
            $current = $current->value ?? $current->name ?? null;
        }

        if (is_iterable($current)) {
            $current = is_string($current) ? json_decode(
                $current,
                true,
                512,
                JSON_THROW_ON_ERROR
            ) : $current;

            return match (true) {
                $current instanceof Collection => $current->contains(
                    $value
                ),
                is_array($current) => in_array($value, $current),
                default => (string) $current === $value
            };
        }

        return (string) $current === $value;
    }

    public static function flatten(iterable $values): array
    {
        return collect($values)
            ->mapWithKeys(fn ($value, $key): array => is_array($value) ? $value : [$key => $value])
            ->toArray();
    }
}
