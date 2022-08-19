<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTrait;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class BelongsTo extends Field implements HasRelationship, BelongsToRelation
{
    use Searchable, WithRelationship, SelectTrait;

    protected static string $view = 'moonshine::fields.select';

    public function save(Model $item): Model
    {
        return $item->{$this->relation()}()
            ->associate($this->requestValue());
    }
}
