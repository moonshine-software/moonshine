<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToOneRelation;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class HasOne extends Field implements HasRelationship, HasFields, OneToOneRelation
{
    use WithFields, WithRelationship;

    protected static string $view = 'has-one';

    protected bool $group = true;

    public function save(Model $item): Model
    {
        $values = $this->requestValue();

        if ($values === false) {
            return $item;
        }

        $related = $this->getRelated($item);
        $primaryKey = $related->getKeyName();
        $table = $related->getTable();

        $item->{$this->relation()}()
            ->updateOrCreate(["$table.$primaryKey" => $values[$primaryKey] ?? null], $values);

        return $item;
    }
}
