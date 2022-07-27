<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;

trait HasManyRelationConceptTrait
{
    public function indexViewValue(Model $item, bool $container = false): \Illuminate\Contracts\View\View
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
            $item->{$this->relation()}()->createMany($this->requestValue());
        }

        return $item;
    }
}
