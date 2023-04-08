<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\HasFullPageMode;
use Leeto\MoonShine\Contracts\Fields\HasJsonValues;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToOneRelation;
use Leeto\MoonShine\Contracts\Fields\RemovableContract;
use Leeto\MoonShine\Traits\Fields\HasOneOrMany;
use Leeto\MoonShine\Traits\Fields\WithFullPageMode;
use Leeto\MoonShine\Traits\Fields\WithJsonValues;
use Leeto\MoonShine\Traits\Fields\WithRelationship;
use Leeto\MoonShine\Traits\Fields\WithResourceMode;
use Leeto\MoonShine\Traits\Removable;
use Leeto\MoonShine\Traits\WithFields;

class HasOne extends Field implements
    HasRelationship,
    HasFields,
    HasJsonValues,
    HasResourceMode,
    HasFullPageMode,
    OneToOneRelation,
    RemovableContract
{
    use WithFields;
    use WithJsonValues;
    use WithResourceMode;
    use WithFullPageMode;
    use WithRelationship;
    use HasOneOrMany;
    use Removable;

    protected static string $view = 'moonshine::fields.has-one';

    protected bool $group = true;
}
