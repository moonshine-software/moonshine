<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

class HasOne extends HasMany
{
    protected static string $view = 'moonshine::fields.has-one';
}
