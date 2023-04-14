<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Traits\Fields\WithRelatedValues;

class BelongsToFilter extends SelectFilter implements
    HasRelationship,
    HasRelatedValues,
    BelongsToRelation
{
    use WithRelatedValues;
}
