<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class BelongsTo extends Field implements HasRelationship, BelongsToRelation
{
    use Searchable;
    use WithRelationship;

    protected static string $view = 'moonshine::fields.select';

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
