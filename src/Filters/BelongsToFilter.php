<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Traits\Fields\WithRelationship;

class BelongsToFilter extends SelectFilter implements HasRelationship, BelongsToRelation
{
    use WithRelationship;
}
