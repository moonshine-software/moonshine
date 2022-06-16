<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasFieldsContract;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Traits\Fields\WithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\WithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\HasManyRelationConceptTrait;

class HasManyThrough extends Field implements HasRelationshipContract, HasFieldsContract
{
    use HasManyRelationConceptTrait;
    use WithRelationshipsTrait, WithFieldsTrait;

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