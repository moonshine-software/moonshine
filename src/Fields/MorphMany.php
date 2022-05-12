<?php

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\FieldHasFieldsContract;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Traits\Fields\FieldWithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\HasManyRelationConceptTrait;

class MorphMany extends BaseField implements FieldHasRelationContract, FieldHasFieldsContract
{
    use HasManyRelationConceptTrait;
    use FieldWithRelationshipsTrait, FieldWithFieldsTrait;

    protected static string $view = 'has-many';
}