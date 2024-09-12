<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

/**
 * @extends HasOne<\Illuminate\Database\Eloquent\Relations\MorphOne>
 */
class MorphOne extends HasOne
{
    protected bool $isMorph = true;
}
