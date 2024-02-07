<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\MorphOne>
 */
class MorphOne extends HasOne
{
    protected bool $isMorph = true;
}
