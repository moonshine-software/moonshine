<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields;

use Illuminate\Database\Eloquent\Model;

interface HasJsonValues
{
    public function jsonValues(Model $item = null): array;
}
