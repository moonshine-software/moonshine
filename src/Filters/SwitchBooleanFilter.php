<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeBool;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\BooleanTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class SwitchBooleanFilter extends Filter implements
    HasDefaultValue,
    DefaultCanBeNumeric,
    DefaultCanBeString,
    DefaultCanBeBool
{
    use BooleanTrait;
    use WithDefaultValue;

    protected static string $view = 'moonshine::filters.switch';
    public string $type = 'checkbox';
}
