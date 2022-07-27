<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Helpers;

final class ConditionHelpers
{
    /**
     * Returns the Boolean value of the condition
     *
     * @param  mixed  $condition
     * @param  bool  $default  Default value. Return if condition not isset
     *
     * @return bool
     */
    public static function boolean(mixed $condition, bool $default): bool
    {
        return is_null($condition)
            ? $default
            : ($condition instanceof \Closure ? $condition() : $condition);
    }
}
