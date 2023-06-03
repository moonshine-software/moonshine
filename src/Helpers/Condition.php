<?php

declare(strict_types=1);

namespace MoonShine\Helpers;

use Closure;

final class Condition
{
    /**
     * Returns the Boolean value of the condition
     *
     * @param  bool  $default  Default value. Return if condition not isset
     *
     */
    public static function boolean(
        Closure|bool|null $condition,
        bool $default
    ): bool {
        return is_null($condition)
            ? $default
            : ($condition instanceof Closure ? $condition() : $condition);
    }
}
