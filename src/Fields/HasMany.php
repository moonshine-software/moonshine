<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToManyRelation;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class HasMany extends Field implements \JsonSerializable, HasRelationship, HasFields, OneToManyRelation
{
    use WithFields, WithRelationship;

    protected bool $group = true;

    public function getView(): string
    {
        return $this->isFullPage() ? 'moonshine::fields.full-fields' : 'moonshine::fields.table-fields';
    }

    public function save(Model $item): Model
    {
        $item->{$this->relation()}()->delete();

        if ($this->requestValue() !== false) {
            if ($this instanceof HasManyThrough) {
                foreach ($this->requestValue() as $values) {
                    $item->{$this->relation()}()->create($values);
                }
            } else {
                $item->{$this->relation()}()->createMany($this->requestValue());
            }
        }

        return $item;
    }
}
