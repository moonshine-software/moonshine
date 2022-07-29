<?php

namespace Leeto\MoonShine\Helpers;

use Closure;

final class Condition
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
            : ($condition instanceof Closure ? $condition() : (bool)$condition);
    }
}
