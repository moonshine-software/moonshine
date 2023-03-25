<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\HasFullPageMode;
use Leeto\MoonShine\Contracts\Fields\HasJsonValues;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToOneRelation;
use Leeto\MoonShine\Traits\Fields\WithFullPageMode;
use Leeto\MoonShine\Traits\Fields\WithJsonValues;
use Leeto\MoonShine\Traits\Fields\WithRelationship;
use Leeto\MoonShine\Traits\Fields\WithResourceMode;
use Leeto\MoonShine\Traits\WithFields;

class HasOne extends Field implements HasRelationship, HasFields, HasJsonValues, HasResourceMode, HasFullPageMode, OneToOneRelation
{
    use WithFields;
    use WithJsonValues;
    use WithResourceMode;
    use WithFullPageMode;
    use WithRelationship;

    protected static string $view = 'moonshine::fields.has-one';

    protected bool $group = true;

    public function save(Model $item): Model
    {
        if ($this->isResourceMode()) {
            return $item;
        }

        $values = $this->requestValue();

        if ($values === false) {
            return $item;
        }

        $related = $this->getRelated($item);
        $primaryKey = $related->getKeyName();
        $table = $related->getTable();

        foreach ($this->getFields() as $field) {
            if ($field instanceof Fileable) {
                $values = $field->hasManyOrOneSave("hidden_{$this->field()}.{$field->field()}", $values);
            }
        }

        $item->{$this->relation()}()
            ->updateOrCreate(["$table.$primaryKey" => $values[$primaryKey] ?? null], $values);

        return $item;
    }
}
