<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields\Relationships;

use Illuminate\Database\Eloquent\Model;

interface HasRelationship
{
    public function relatedValues(Model $item): array;

    public function values(): array;
}
