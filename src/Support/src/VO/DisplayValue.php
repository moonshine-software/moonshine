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
        return match (true) {
            $this->value instanceof BackedEnum => (string) $this->value->value,
            $this->value instanceof UnitEnum => $this->value->name,
            $this->value instanceof Stringable => (string) $this->value,
            \is_scalar($this->value) => (string) $this->value,
            default => '',
        };
    }
}
