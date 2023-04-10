<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasFullPageMode;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use MoonShine\Contracts\Fields\Relationships\OneToManyRelation;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Traits\Fields\HasOneOrMany;
use MoonShine\Traits\Fields\WithFullPageMode;
use MoonShine\Traits\Fields\WithJsonValues;
use MoonShine\Traits\Fields\WithRelationship;
use MoonShine\Traits\Fields\WithResourceMode;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;

class HasMany extends Field implements
    HasRelationship,
    HasFields,
    HasJsonValues,
    HasResourceMode,
    HasFullPageMode,
    OneToManyRelation,
    RemovableContract
{
    use WithResourceMode;
    use WithFullPageMode;
    use WithFields;
    use WithJsonValues;
    use WithRelationship;
    use HasOneOrMany;
    use Removable;

    protected static string $view = 'moonshine::fields.has-many';

    protected bool $group = true;

    public function indexViewValue(Model $item, bool $container = false): string
    {
        if ($this->onlyCount) {
            return (string)$item->{$this->relation()}->count();
        }

        $columns = [];
        $values = [];

        foreach ($this->getFields() as $field) {
            $columns[$field->field()] = $field->label();
        }

        foreach ($item->{$this->field()} as $index => $data) {
            foreach ($this->getFields() as $field) {
                $values[$index][$field->field()] = $field->indexViewValue($data, false);
            }
        }

        return view('moonshine::ui.table', [
            'columns' => $columns,
            'values' => $values,
        ])->render();
    }

    public function exportViewValue(Model $item): string
    {
        return '';
    }
}
