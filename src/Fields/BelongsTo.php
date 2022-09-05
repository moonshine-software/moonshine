<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\HasRelatedValues;
use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;

class BelongsTo extends Field implements HasRelationship, HasRelatedValues
{
    use Searchable;

    protected static string $component = 'BelongsToField';
}
