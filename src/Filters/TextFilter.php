<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Traits\Fields\WithInputExtensions;
use MoonShine\Traits\Fields\WithMask;

class TextFilter extends Filter
{
    use WithMask;
    use WithInputExtensions;

    protected static string $view = 'moonshine::filters.text';
}
