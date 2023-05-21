<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithInputExtensions;
use MoonShine\Traits\Fields\WithMask;

class TextFilter extends Filter implements
    HasDefaultValue,
    DefaultCanBeString
{
    use WithMask;
    use WithInputExtensions;
    use WithDefaultValue;

    public string $type = 'text';

    protected static string $view = 'moonshine::filters.text';
}
