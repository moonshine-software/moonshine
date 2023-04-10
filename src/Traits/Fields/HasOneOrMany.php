<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Fields\Field;
use MoonShine\Fields\HasOne;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;

/**
 * @mixin Field
 */
trait HasOneOrMany
{
    public function save(Model $item): Model
    {
        if ($this->isResourceMode()) {
            return $item;
        }

        $related = $this->getRelated($item);
        $foreignKeyName = $item->{$this->relation()}()->getForeignKeyName();
        $primaryKey = $related->getKeyName();

        $currentIdentities = [];
        $prevIdentities = $this instanceof HasOne
            ? []
            : $item->{$this->relation()}
                ->pluck($primaryKey)
                ->toArray();

        if ($this->requestValue() !== false) {
            foreach ($this->requestValue() as $index => $values) {
                $identity = $this instanceof HasOne ? $item->{$this->relation()}?->getKey() : null;

                foreach ($this->getFields() as $field) {
                    if (! $this instanceof HasOne && $field instanceof ID) {
                        $identity = $values[$field->field()] ?? null;
                        $currentIdentities[$identity] = $identity;
                    }

                    if ($field instanceof Fileable) {
                        $values = $field->hasManyOrOneSave("hidden_{$this->field()}.$index.{$field->field()}", $values);
                    }

                    if ($field instanceof Json && $field->isKeyValue()) {
                        $values[$field->field()] = collect($values[$field->field()] ?? [])
                            ->mapWithKeys(fn ($data) => [$data['key'] => $data['value']]);
                    }
                }

                $values[$foreignKeyName] = $item->getKey()
                    ?? $values[$foreignKeyName]
                    ?? null;

                $item->{$this->relation()}()->updateOrCreate([
                    $primaryKey => $identity,
                ], $values);
            }
        } elseif ($this instanceof HasOne) {
            $item->{$this->relation()}()->delete();
        }

        if (! $this instanceof HasOne) {
            $item->{$this->relation()}()
                ->whereIn($primaryKey, collect($prevIdentities)->diff($currentIdentities)->toArray())
                ->delete();
        }

        return $item;
    }
}
