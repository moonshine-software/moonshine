<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\WithPivot;
use Leeto\MoonShine\Traits\WithFields;

class BelongsToManyFilter extends Filter implements HasRelationship
{
    use Searchable;
    use WithFields;
    use WithPivot;

    protected static string $component = 'BelongsToManyFilter';
}
