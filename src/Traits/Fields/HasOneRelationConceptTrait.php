<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;

trait HasOneRelationConceptTrait
{
    protected static bool $toOne = true;

    protected static bool $hasOne = true;

    public function save(Model $item): Model
    {
        $values = $this->requestValue();

        if ($values === false) {
            return $item;
        }

        $related = $item->{$this->relation()}()->getRelated();
        $primaryKey = $related->getKeyName();
        $table = $related->getTable();

        $item->{$this->relation()}()
            ->updateOrCreate(["$table.$primaryKey" => $values[$primaryKey] ?? null], $values);

        return $item;
    }
}
