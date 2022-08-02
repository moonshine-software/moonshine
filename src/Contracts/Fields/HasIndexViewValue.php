<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

use Illuminate\Database\Eloquent\Model;

interface HasIndexViewValue
{
    public function indexViewValue(Model $item, bool $container = true): mixed;
}
