<?php

namespace Leeto\MoonShine\Filters;


use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Traits\Fields\WithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableTrait;

class BelongsToFilter extends Filter implements HasRelationshipContract
{
    use SearchableTrait, WithRelationshipsTrait;

    public static bool $toOne = true;

    public static string $view = 'select';
}