<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\RangeField;
use MoonShine\Traits\Fields\RangeTrait;

class Range extends Number implements DefaultCanBeArray, RangeField
{
    use RangeTrait;

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;
}
