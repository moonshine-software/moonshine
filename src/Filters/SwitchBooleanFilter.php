<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\BooleanTrait;

class SwitchBooleanFilter extends Filter
{
    use BooleanTrait;

    protected static string $view = 'moonshine::filters.switch';
}
