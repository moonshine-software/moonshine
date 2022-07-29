<?php

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTrait;

class SelectFilter extends Filter
{
    use Searchable, SelectTrait, CanBeMultiple;

    public static string $view = 'select';
}
