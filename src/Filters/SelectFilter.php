<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTrait;
use Leeto\MoonShine\Traits\Fields\WithOptions;

class SelectFilter extends Filter
{
    use Searchable, SelectTrait, CanBeMultiple, WithOptions;

    public static string $view = 'moonshine::filters.select';
}
