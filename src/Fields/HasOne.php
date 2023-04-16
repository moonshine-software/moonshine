<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class HasOne extends HasMany
{
    protected static string $view = 'moonshine::fields.has-one';
}
