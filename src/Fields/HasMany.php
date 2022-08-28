<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\HasResource;
use Leeto\MoonShine\Contracts\Fields\Tableable;

class HasMany extends Field implements HasRelationship, Tableable, HasResource
{
    protected static string $component = 'HasManyField';
}
