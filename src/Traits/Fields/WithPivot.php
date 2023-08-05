<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;

trait WithPivot
{
    public function pivotValue(Model $item, $id): Model
    {
        $pivotItem = $this->pivotItem($item, $id);

        return $pivotItem
            ? $pivotItem->{$this->getPivotAs($pivotItem)}
            : $item->newInstance();
    }

    public function pivotItem(Model $item, $id): ?Model
    {
        return $item->isNotEmpty()
            ? $item->firstWhere($item->first()->getKeyName(), '=', $id)
            : null;
    }

    public function getPivotAs(Model $item): int|string|null
    {
        return array_key_first($item->getRelations());
    }
}
