<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Contracts\Fields\Relationships\ManyToManyRelation;
use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\CheckboxTrait;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\SelectTransform;
use MoonShine\Traits\Fields\WithPivot;
use MoonShine\Traits\Fields\WithRelationship;
use MoonShine\Traits\WithFields;

class BelongsToManyFilter extends Filter implements HasRelationship, HasFields, ManyToManyRelation
{
    use CheckboxTrait;
    use Searchable;
    use SelectTransform;
    use WithFields;
    use WithPivot;
    use WithRelationship;
    use CanBeMultiple;

    protected static string $view = 'moonshine::filters.belongs-to-many';

    protected bool $group = true;
}
