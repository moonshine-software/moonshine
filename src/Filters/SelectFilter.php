<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\SelectTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class SelectFilter extends Filter implements
    HasDefaultValue,
    DefaultCanBeArray,
    DefaultCanBeString,
    DefaultCanBeNumeric
{
    use CanBeMultiple;
    use Searchable;
    use SelectTrait;
    use WithDefaultValue;

    protected string $view = 'moonshine::filters.select';
}
