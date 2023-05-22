<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Fields\Field;
use MoonShine\Fields\HasOne;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use MoonShine\Fields\SlideField;

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

                        $model = null;
                        if(! is_null($item->{$this->relation()})) {
                            $model = $this instanceof HasOne
                                ? $item->{$this->relation()}
                                : ($item->{$this->relation()}[$index] ?? null);
                        }

                        $values = $field->hasManyOrOneSave(
                            "hidden_{$this->field()}.$index.{$field->field()}",
                            $values,
                            $model
                        );

                        if($field->isDeleteFiles()) {
                            $storedValues = $model?->{$field->field()};

                            $field->checkAndDelete(
                                $storedValues,
                                $values[$field->field()]
                            );
                        }
                    }

                    if ($field instanceof SlideField) {
                        $values[$field->fromField] = $values[$field->field()][$field->fromField] ?? '';
                        $values[$field->toField] = $values[$field->field()][$field->toField] ?? '';
                        unset($values[$field->field()]);
                    }

                    if ($field instanceof Json && $field->isKeyValue()) {
                        $values[$field->field()] = collect($values[$field->field()] ?? [])
                            ->mapWithKeys(static fn ($data) => [$data['key'] => $data['value']])
                            ->filter();
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
            foreach ($this->getFields()->onlyDeletableFileFields() as $field) {
                if (! empty($item->{$this->relation()}?->{$field->field()})) {
                    $field->deleteFile(
                        $item->{$this->relation()}->{$field->field()}
                    );
                }
            }
            $item->{$this->relation()}()->delete();
        }

        if (! $this instanceof HasOne) {
            $ids = collect($prevIdentities)->diff($currentIdentities)->toArray();

            foreach ($this->getFields()->onlyDeletableFileFields() as $field) {
                foreach ($item->{$this->relation()} as $value) {
                    if(in_array($value->{$primaryKey}, $ids)) {
                        $field->deleteFile($value->{$field->field()});
                    }
                }
            }

            $item->{$this->relation()}()
                ->whereIn($primaryKey, $ids)
                ->delete();
        }

        return $item;
    }

    public function afterDelete(Model $item): void
    {
        foreach ($this->getFields()->onlyFileFields() as $field) {
            if (! empty($item->{$this->relation()})) {
                if($this instanceof HasOne) {
                    $field->afterDelete($item->{$this->relation()});
                } else {
                    $item->{$this->relation()}->each(
                        fn ($itemRelation) => $field->afterDelete($itemRelation)
                    );
                }
            }
        }
    }
}
