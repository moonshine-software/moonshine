<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithRelatedValues;

class BelongsTo extends Select implements
    HasRelationship,
    HasRelatedValues,
    BelongsToRelation,
    HasAsyncSearch
{
    use WithRelatedValues;
    use WithAsyncSearch;

    public function isMultiple(): bool
    {
        return false;
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue() === false) {
            if ($this->isNullable()) {
                return $item->{$this->relation()}()
                    ->dissociate();
            }

            return $item;
        }

        $value = $item->{$this->relation()}()
            ->getRelated()
            ->findOrFail($this->requestValue());

        return $item->{$this->relation()}()
            ->associate($value);
    }
}
