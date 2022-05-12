<?php

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\SearchableSelectFieldTrait;

class SelectFilter extends BaseFilter
{
    use SearchableSelectFieldTrait;

    public static string $view = 'select';
}