<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationshipContract;
use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelationshipContract;
use Leeto\MoonShine\Traits\Fields\WithRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;

class BelongsTo extends Field implements HasRelationshipContract, BelongsToRelationshipContract
{
    use Searchable, WithRelationship;

    protected static string $view = 'select';

    public function save(Model $item): Model
    {
        return $item->{$this->relation()}()
            ->associate($this->requestValue());
    }
}
