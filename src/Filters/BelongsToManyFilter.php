<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Traits\Fields\SelectTransformerTrait;
use Leeto\MoonShine\Traits\Fields\WithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\WithPivotTrait;
use Leeto\MoonShine\Traits\Fields\WithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableTrait;

class BelongsToManyFilter extends Filter implements HasRelationshipContract
{
    use SelectTransformerTrait, WithRelationshipsTrait, WithFieldsTrait, WithPivotTrait;
    use SearchableTrait;

    public static string $view = 'belongs-to-many';
}
