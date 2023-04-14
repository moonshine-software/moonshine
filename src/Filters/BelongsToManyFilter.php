<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Traits\Fields\CheckboxTrait;
use MoonShine\Traits\Fields\SelectTransform;
use MoonShine\Traits\Fields\WithPivot;
use MoonShine\Traits\Fields\WithRelatedValues;
use MoonShine\Traits\WithFields;

class BelongsToManyFilter extends SelectFilter implements
    HasRelationship,
    HasRelatedValues,
    HasFields
{
    use CheckboxTrait;
    use SelectTransform;
    use WithFields;
    use WithPivot;
    use WithRelatedValues;

    protected static string $view = 'moonshine::filters.belongs-to-many';

    protected bool $group = true;
}
