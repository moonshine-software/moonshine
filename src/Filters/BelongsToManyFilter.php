<?php

namespace Leeto\MoonShine\Filters;


use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Traits\Fields\FieldSelectTransformer;
use Leeto\MoonShine\Traits\Fields\FieldWithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithPivotTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableSelectFieldTrait;

class BelongsToManyFilter extends BaseFilter implements FieldHasRelationContract
{
    use FieldSelectTransformer, FieldWithRelationshipsTrait, FieldWithFieldsTrait, FieldWithPivotTrait;
    use SearchableSelectFieldTrait;

    public static string $view = 'belongs-to-many';
}