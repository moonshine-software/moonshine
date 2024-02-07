<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\MorphMany>
 */
class MorphMany extends HasMany
{
    protected bool $isMorph = true;
}
