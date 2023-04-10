<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields;

use Illuminate\Database\Eloquent\Model;

interface HasFormViewValue
{
    public function formViewValue(Model $item): mixed;
}
