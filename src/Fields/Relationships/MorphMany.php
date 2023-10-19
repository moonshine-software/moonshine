<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

class MorphMany extends HasMany
{
    protected bool $isMorph = true;
}
