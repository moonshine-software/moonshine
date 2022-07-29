<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShine\Contracts\Fields\HasFieldsContract;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationshipContract;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToManyRelationshipContract;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class HasMany extends Field implements HasRelationshipContract, HasFieldsContract, OneToManyRelationshipContract
{
    use WithFields, WithRelationship;

    protected static string $view = 'has-many';

    protected bool $group = true;

    public function indexViewValue(Model $item, bool $container = false): string
    {
        $columns = [];
        $values = [];

        foreach ($this->getFields() as $field) {
            $columns[$field->field()] = $field->label();
        }

        foreach ($item->{$this->field()} as $index => $item) {
            foreach ($this->getFields() as $field) {
                $values[$index][$field->field()] = $field->indexViewValue($item, false);
            }
        }

        return view('moonshine::shared.table', [
            'columns' => $columns,
            'values' => $values
        ]);
    }

    public function exportViewValue(Model $item): string
    {
        return '';
    }

    public function save(Model $item): Model
    {
        $item->{$this->relation()}()->delete();

        if ($this->requestValue() !== false) {
            if ($this instanceof HasManyThrough) {
                foreach ($this->requestValue() as $values) {
                    $item->{$this->relation()}()->create($values);
                }
            } else {
                $item->{$this->relation()}()->createMany($this->requestValue());
            }
        }

        return $item;
    }
}
