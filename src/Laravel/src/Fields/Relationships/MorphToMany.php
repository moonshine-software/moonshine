<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Illuminate\Database\Eloquent\Model;

/**
 * @extends BelongsToMany<\Illuminate\Database\Eloquent\Relations\MorphToMany<Model, Model>>
 */
class MorphToMany extends BelongsToMany
{
    protected bool $isMorph = true;
}
