<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\ManyToManyRelation;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\CheckboxTrait;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTransform;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Leeto\MoonShine\Traits\Fields\WithPivot;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class BelongsToManyFilter extends Filter implements HasRelationship, ManyToManyRelation
{
    use CheckboxTrait;
    use Searchable;
    use SelectTransform;
    use WithFields;
    use WithPivot;
    use WithRelationship;
    use CanBeMultiple;

    public static string $view = 'moonshine::filters.belongs-to-many';

    protected bool $group = true;
}
