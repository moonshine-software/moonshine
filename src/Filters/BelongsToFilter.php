<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;

class BelongsToFilter extends Filter implements HasRelationship
{
    use Searchable;

    public static string $component = 'SelectFilter';
}
