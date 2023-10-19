<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

class MorphToMany extends BelongsToMany
{
    protected bool $isMorph = true;
}
