<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Casts\ModelCast;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Fields\ID;
use MoonShine\Traits\Fields\HasOneOrMany;
use MoonShine\Traits\Fields\WithJsonValues;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;

class HasMany extends ModelRelationField implements
    HasFields,
    HasJsonValues,
    RemovableContract
{
    use WithFields;
    use WithJsonValues;
    use HasOneOrMany;
    use Removable;

    protected string $view = 'moonshine::fields.relationships.has-many';

    protected bool $isGroup = true;

    protected function resolvePreview(): string
    {
        $values = $this->toValue();
        $column = $this->getResource()->column();

        if ($this->isRawMode()) {
            return $values
                ->map(fn (Model $item) => $item->{$column})
                ->implode(';');
        }

        $fields = $this->getFields()
            ->indexFields()
            ->prepend(ID::make())
            ->toArray();

        return (string) table($fields, $values)
            ->cast(ModelCast::make($this->getRelation()->getRelated()::class))
            ->preview();
    }
}
