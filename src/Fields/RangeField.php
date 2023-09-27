<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Traits\Fields\RangeTrait;

class RangeField extends Number implements DefaultCanBeArray
{
    use RangeTrait;

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;
}
