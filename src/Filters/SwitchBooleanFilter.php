<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\BooleanTrait;

class SwitchBooleanFilter extends Filter
{
    use BooleanTrait;

    public static string $component = 'SwitchFilter';
}
