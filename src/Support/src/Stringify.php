<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Stringable;
use TypeError;
use UnitEnum;

final class Stringify
{
    public static function value(mixed $value): string
    {
        if ($value instanceof UnitEnum) {
            $value = new EnumToString($value)->convert();
        }

        if (\is_scalar($value) || $value === null || $value instanceof Stringable || \is_resource($value)) {
            return (string) $value;
        }

        throw new TypeError('Cannot convert ' . get_debug_type($value) . ' to string.');
    }
}
