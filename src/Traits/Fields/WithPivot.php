<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;

trait WithPivot
{
    public function getPivotAs(Model $item): int|string|null
    {
        return array_key_first($item->getRelations());
    }

    public function pivotItem(Model $item, $id): Model|null
    {
        $value = $this->formViewValue($item);

        return $value->isNotEmpty()
            ? $value->firstWhere($value->first()->getKeyName(), '=', $id)
            : null;
    }

    public function pivotValue(Model $item, $id): Model
    {
        $pivotItem = $this->pivotItem($item, $id);

        return $pivotItem->{$this->getPivotAs($pivotItem)}
            ?? $item->newInstance();
    }
}
