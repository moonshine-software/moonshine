<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTrait;

class SelectFilter extends Filter
{
    use CanBeMultiple;
    use Searchable;
    use SelectTrait;

    protected static string $view = 'moonshine::filters.select';
}
