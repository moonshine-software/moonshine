<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\ManyToManyRelation;
use Leeto\MoonShine\Traits\Fields\CanBeSelect;
use Leeto\MoonShine\Traits\Fields\CheckboxTrait;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTrait;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Leeto\MoonShine\Traits\Fields\WithPivot;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class BelongsToManyFilter extends Filter implements HasRelationship, ManyToManyRelation
{
    use Searchable;
    use CanBeSelect;
    use WithFields;
    use WithPivot;
    use WithRelationship;
    use CheckboxTrait;
    use SelectTrait;

    protected bool $group = true;

    public function getView(): string
    {
        return $this->isSelect()
            ? 'moonshine::fields.select'
            : 'moonshine::fields.multi-checkbox';
    }
}
