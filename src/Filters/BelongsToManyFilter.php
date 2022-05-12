<?php

namespace Leeto\MoonShine\Filters;


use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Traits\Fields\FieldWithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithPivotTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;

class BelongsToManyFilter extends BaseFilter implements FieldHasRelationContract
{
    use FieldWithRelationshipsTrait, FieldWithFieldsTrait, FieldWithPivotTrait;

    public static string $view = 'multi-checkbox';
}