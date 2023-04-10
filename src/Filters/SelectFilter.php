<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\SelectTrait;

class SelectFilter extends Filter
{
    use CanBeMultiple;
    use Searchable;
    use SelectTrait;

    protected static string $view = 'moonshine::filters.select';
}
