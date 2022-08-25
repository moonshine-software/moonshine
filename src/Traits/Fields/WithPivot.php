<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;

trait WithPivot
{
    public function getPivotAs(): int|string|null
    {
        return array_key_first($this->value()->getRelations());
    }

    public function pivotItem($id): ?Model
    {
        return $this->value()->firstWhere($this->value()->getKeyName(), '=', $id);
    }

    public function pivotValue($id): Model
    {
        $pivotItem = $this->pivotItem($id);

        return $pivotItem
            ? $pivotItem->{$this->getPivotAs()} ?? $pivotItem
            : $this->value();
    }
}
