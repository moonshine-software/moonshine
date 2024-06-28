<?php

namespace MoonShine\Traits\Enum;

use UnitEnum;

/**
 * @mixin UnitEnum
 */
trait HasTryFromStringEnumTrait
{
    public static function tryFromString(string $value): ?self
    {
        if (method_exists(self::class, 'toString') === false) {
            return null;
        }

        $allCases = self::cases();

        $foundCases = array_filter($allCases, fn($case): bool => mb_strtolower($case->toString()) === mb_strtolower($value));

        return $foundCases !== [] ? head($foundCases) : null;
    }
}
