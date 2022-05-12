<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\FieldHasFieldsContract;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Traits\Fields\FieldWithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\HasManyRelationConceptTrait;

class HasManyThrough extends BaseField implements FieldHasRelationContract, FieldHasFieldsContract
{
    use HasManyRelationConceptTrait;
    use FieldWithRelationshipsTrait, FieldWithFieldsTrait;

    protected static string $view = 'has-many';

    public function save(Model $item): Model
    {
        $item->{$this->relation()}()->delete();

        if($this->requestValue() !== false) {
            foreach ($this->requestValue() as $values) {
                $item->{$this->relation()}()->create($values);
            }
        }

        return $item;
    }
}