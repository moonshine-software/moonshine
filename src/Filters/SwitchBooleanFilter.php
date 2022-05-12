<?php

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\BooleanFieldTrait;

class SwitchBooleanFilter extends BaseFilter
{
    use BooleanFieldTrait;

    public static string $view = 'switch';
}