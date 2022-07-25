<?php

namespace Leeto\MoonShine\Utilities;

final class Helpers
{
    /**
     * Returns the Boolean value of the condition
     *
     * @param  mixed  $condition
     * @param  bool  $default  Default value. Return if condition not isset
     *
     * @return bool
     */
    public static function executeBooleanCondition(mixed $condition, bool $default): bool
    {
        return is_null($condition)
            ? $default
            : ($condition instanceof \Closure ? $condition() : $condition);
    }
}
