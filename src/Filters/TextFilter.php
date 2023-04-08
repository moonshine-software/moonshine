<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\WithMask;

class TextFilter extends Filter
{
    use WithMask;

    protected static string $view = 'moonshine::filters.text';
}
