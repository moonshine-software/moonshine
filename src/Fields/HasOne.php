<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\HasFieldsContract;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Traits\Fields\WithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\WithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\HasOneRelationConceptTrait;

class HasOne extends Field implements HasRelationshipContract, HasFieldsContract
{
    use HasOneRelationConceptTrait;
    use WithRelationshipsTrait, WithFieldsTrait;

    protected static string $view = 'has-one';
}
