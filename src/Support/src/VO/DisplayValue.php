<?php

declare(strict_types=1);

namespace MoonShine\Support\VO;

use BackedEnum;
use Stringable;
use UnitEnum;

/**
 * Resolves an arbitrary value into a human-readable string label.
 */
final readonly class DisplayValue implements Stringable
{
    public function __construct(
        private mixed $value,
    ) {
    }

    public function __toString(): string
    {
        $value = $this->value;

        return match (true) {
            $value instanceof UnitEnum => match (true) {
                method_exists($value, 'toString') => (new self($value->toString()))->__toString(),
                $value instanceof BackedEnum => (string) $value->value,
                default => $value->name,
            },
            $value instanceof Stringable => (string) $value,
            \is_scalar($value) => (string) $value,
            default => '',
        };
    }
}
