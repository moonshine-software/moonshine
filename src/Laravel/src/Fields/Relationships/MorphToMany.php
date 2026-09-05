<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

/**
 * @extends BelongsToMany<\Illuminate\Database\Eloquent\Relations\MorphToMany<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model>>
 */
class MorphToMany extends BelongsToMany
{
    protected bool $isMorph = true;
}
