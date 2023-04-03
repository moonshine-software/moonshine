<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

use Illuminate\Database\Eloquent\Model;

interface HasPivot
{
    public function pivotItem(Model $item, $id): ?Model;

    public function pivotValue(Model $item, $id): Model;
}
