<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\HasResource;
use Leeto\MoonShine\Traits\WithFields;

class HasOne extends Field implements HasRelationship, HasFields, HasResource
{
    use WithFields;

    protected static string $component = 'HasOneField';
}
