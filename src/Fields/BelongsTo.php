<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\WithOnlySelect;
use MoonShine\Traits\Fields\WithRelationship;

class BelongsTo extends Select implements HasRelationship, BelongsToRelation
{
    use WithRelationship;
    use WithOnlySelect;
    use Searchable;

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
