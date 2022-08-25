<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

use Illuminate\Support\Collection;

interface HasRelatedValues
{
    public function relatedValues(): Collection;
}
