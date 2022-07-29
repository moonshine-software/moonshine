<?php

namespace Leeto\MoonShine\Filters;


use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationshipContract;
use Leeto\MoonShine\Contracts\Fields\Relationships\ManyToManyRelationshipContract;
use Leeto\MoonShine\Traits\Fields\CheckboxTrait;
use Leeto\MoonShine\Traits\Fields\SelectTransform;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Leeto\MoonShine\Traits\Fields\WithRelationship;
use Leeto\MoonShine\Traits\Fields\WithPivot;
use Leeto\MoonShine\Traits\Fields\Searchable;

class BelongsToManyFilter extends Filter implements HasRelationshipContract, ManyToManyRelationshipContract
{
    use Searchable, SelectTransform, WithFields, WithPivot, WithRelationship, CheckboxTrait;

    public static string $view = 'belongs-to-many';

    protected bool $group = true;
}
