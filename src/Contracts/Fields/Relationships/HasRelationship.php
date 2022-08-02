<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields\Relationships;

interface HasRelationship
{
    public function resolveRelatedValues(array $values): array;

    public function values(): array;
}
