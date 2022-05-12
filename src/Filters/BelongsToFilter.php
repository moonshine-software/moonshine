<?php

namespace Leeto\MoonShine\Filters;


use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableSelectFieldTrait;

class BelongsToFilter extends BaseFilter implements FieldHasRelationContract
{
    use SearchableSelectFieldTrait, FieldWithRelationshipsTrait;

    public static bool $toOne = true;

    public static string $view = 'select';
}