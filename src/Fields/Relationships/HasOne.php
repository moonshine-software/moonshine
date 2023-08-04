<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Casts\ModelCast;
use MoonShine\Fields\ID;

class HasOne extends HasMany
{
    protected string $view = 'moonshine::fields.relationships.has-one';

    protected function resolvePreview(): string
    {
        if (is_null($this->toValue())) {
            return '';
        }

        $this->setValue(
            collect([
                $this->toValue(),
            ])
        );

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
            ->vertical()
            ->preview();
    }
}
