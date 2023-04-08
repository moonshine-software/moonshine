<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\HasFullPageMode;
use Leeto\MoonShine\Contracts\Fields\HasJsonValues;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToManyRelation;
use Leeto\MoonShine\Contracts\Fields\RemovableContract;
use Leeto\MoonShine\Traits\Fields\HasOneOrMany;
use Leeto\MoonShine\Traits\Fields\WithFullPageMode;
use Leeto\MoonShine\Traits\Fields\WithJsonValues;
use Leeto\MoonShine\Traits\Fields\WithRelationship;
use Leeto\MoonShine\Traits\Fields\WithResourceMode;
use Leeto\MoonShine\Traits\Removable;
use Leeto\MoonShine\Traits\WithFields;

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
