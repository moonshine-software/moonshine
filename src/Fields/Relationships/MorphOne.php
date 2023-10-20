<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

class MorphOne extends HasOne
{
    protected bool $isMorph = true;
}
