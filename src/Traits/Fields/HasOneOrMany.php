<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Fields\Field;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use MoonShine\Fields\Relationships\HasOne;
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
                $fields = collect();

                foreach ($this->getFields()->onlyFields() as $field) {
                    if (! $this instanceof HasOne && $field instanceof ID) {
                        $identity = $values[$field->column()] ?? null;
                        $currentIdentities[$identity] = $identity;
                    }

                    if ($field instanceof Fileable) {
                        $field->setParentRequestValueKey(
                            $this->column() . "." . $index
                        );

                        $values[$field->column()] = $field->hasManyOrOneSave(
                            $values[$field->column()] ?? null
                        );

                        if ($field->isDeleteFiles()) {
                            $model = $this instanceof HasOne
                                ? $item->{$this->relation()}
                                : $item->{$this->relation()}[$index] ?? null;

                            $storedValues = $model?->{$field->column()};

                            $field->checkAndDelete(
                                $storedValues,
                                $values[$field->column()]
                            );
                        }
                    }

                    if ($field instanceof SlideField) {
                        $values[$field->fromField] = $values[$field->column()][$field->fromField] ?? '';
                        $values[$field->toField] = $values[$field->column()][$field->toField] ?? '';
                        unset($values[$field->column()]);
                    }

                    if ($field instanceof Json && $field->isKeyOrOnlyValue()) {
                        $values[$field->column()] = collect(
                            $values[$field->column()] ?? []
                        )
                            ->mapWithKeys(
                                static fn (
                                    $data,
                                    $key
                                ): array => $field->isOnlyValue() ? [$key => $data['value']] : [$data['key'] => $data['value']]
                            )
                            ->filter();
                    }

                    $fields->push($field);
                }

                $values[$foreignKeyName] = $item->getKey()
                    ?? $values[$foreignKeyName]
                    ?? null;

                $item->{$this->relation()}()->updateOrCreate([
                    $primaryKey => $identity,
                ], $values);

                $items = $this instanceof HasOne
                    ? array_filter([$item->{$this->relation()}])
                    : $item->{$this->relation()};

                foreach ($items as $newItem) {
                    $fields->each(
                        static fn (FormElement $field) => $field->afterSave(
                            $newItem
                        )
                    );
                }
            }
        } elseif ($this instanceof HasOne) {
            foreach ($this->getFields()->onlyDeletableFileFields() as $field) {
                if (! empty($item->{$this->relation()}?->{$field->column()})) {
                    $field->deleteFile(
                        $item->{$this->relation()}->{$field->column()}
                    );
                }
            }
            $item->{$this->relation()}()->delete();
        }

        if (! $this instanceof HasOne) {
            $ids = collect($prevIdentities)->diff($currentIdentities)->toArray();

            foreach ($this->getFields()->onlyDeletableFileFields() as $field) {
                foreach ($item->{$this->relation()} as $value) {
                    if (in_array($value->{$primaryKey}, $ids)) {
                        if ($field->isMultiple()) {
                            foreach ($value->{$field->column()} as $fileItem) {
                                $field->deleteFile($fileItem);
                            }
                        } else {
                            $field->deleteFile($value->{$field->column()});
                        }
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
                if ($this instanceof HasOne) {
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
