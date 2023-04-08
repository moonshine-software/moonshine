<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class BelongsToFilter extends SelectFilter implements HasRelationship, BelongsToRelation
{
    use WithRelationship;
}
