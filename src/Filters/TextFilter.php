<?php

namespace Leeto\MoonShine\Filters;


use Leeto\MoonShine\Traits\Fields\WithMask;

class TextFilter extends Filter
{
    use WithMask;

    public static string $view = 'text';
}
