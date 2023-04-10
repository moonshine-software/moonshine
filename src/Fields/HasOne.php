<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasFullPageMode;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use MoonShine\Contracts\Fields\Relationships\OneToOneRelation;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Traits\Fields\HasOneOrMany;
use MoonShine\Traits\Fields\WithFullPageMode;
use MoonShine\Traits\Fields\WithJsonValues;
use MoonShine\Traits\Fields\WithRelationship;
use MoonShine\Traits\Fields\WithResourceMode;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;

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
