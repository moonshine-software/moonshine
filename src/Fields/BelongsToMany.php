<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\HasPivot;
use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\WithPivot;
use Leeto\MoonShine\Traits\WithFields;

class BelongsToMany extends Field implements HasRelationship, HasPivot, HasFields
{
    use WithFields;
    use WithPivot;
    use Searchable;

    protected static string $component = 'BelongsToManyField';
}
