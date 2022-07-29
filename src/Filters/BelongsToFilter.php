<?php

namespace Leeto\MoonShine\Filters;


use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationshipContract;
use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelationshipContract;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class BelongsToFilter extends Filter implements HasRelationshipContract, BelongsToRelationshipContract
{
    use Searchable, WithRelationship;

    public static string $view = 'select';
}
