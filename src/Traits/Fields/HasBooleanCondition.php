<?php

namespace Leeto\MoonShine\Traits\Fields;

trait HasBooleanCondition
{
    /**
     * Returns the Boolean value of the condition
     *
     * @param  mixed  $condition
     * @param  bool  $default  Default value. Return if condition not isset
     *
     * @return bool
     */
    protected function executeBooleanCondition(mixed $condition, bool $default): bool
    {
        return is_null($condition)
            ? $default
            : ($condition instanceof \Closure ? $condition() : $condition);
    }
}
