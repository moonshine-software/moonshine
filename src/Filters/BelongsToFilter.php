<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class BelongsToFilter extends Filter implements HasRelationship, BelongsToRelation
{
    use CanBeMultiple;
    use Searchable;
    use WithRelationship;

    public static string $view = 'select';
}
