<?php

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;

trait FieldWithPivotTrait
{
    public function getPivotAs(Model $item)
    {
        return array_key_first($item->getRelations());
    }

    public function pivotItem(Model $item, $id): Model|null
    {
        return $this->formViewValue($item)
            ->firstWhere('id', '=', $id);
    }

    public function pivotValue(Model $item, $id): Model
    {
        $pivotItem = $this->pivotItem($item, $id);

        return $pivotItem
            ? $pivotItem->{$this->getPivotAs($pivotItem)} ?? $pivotItem
            : $item;
    }
}