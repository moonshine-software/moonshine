<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields\Relationships;

use Illuminate\Support\Collection;

interface HasRelationship
{
    public function resolveRelatedValues(Collection $values): array;

    public function values(): array;
}
