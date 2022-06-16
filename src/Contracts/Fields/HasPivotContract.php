<?php

namespace Leeto\MoonShine\Contracts\Fields;


use Illuminate\Database\Eloquent\Model;

interface HasPivotContract
{
    public function pivotItem(Model $item, $id): Model|null;

    public function pivotValue(Model $item, $id): Model;
}