<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Decorations\Tab;

final class Fields extends Collection
{
    /**
     * @param  string  $resource
     * @param  Field|null  $default
     * @return ?Field
     */
    public function findFieldByResourceClass(string $resource, Field $default = null): ?Field
    {
        return $this->onlyFields()->first(function (Field $field) use ($resource) {
            return $field->resource()
                && $field->resource()::class === $resource;
        }, $default);
    }

    /**
     * @param  string  $relation
     * @param  Field|null  $default
     * @return ?Field
     */
    public function findFieldByRelation(string $relation, Field $default = null): ?Field
    {
        return $this->onlyFields()->first(function (Field $field) use ($relation) {
            return $field->relation() === $relation;
        }, $default);
    }

    /**
     * @param  string  $column
     * @param  Field|null  $default
     * @return ?Field
     */
    public function findFieldByColumn(string $column, Field $default = null): ?Field
    {
        return $this->onlyFields()->first(function (Field $field) use ($column) {
            return $field->column() === $column;
        }, $default);
    }

    /**
     * @return Fields<Field>
     */
    public function onlyFields(): Fields
    {
        return $this->flatMap(function ($field) {
            if ($field instanceof Tab) {
                return collect($field->getFields())->filter(fn($f) => $f instanceof Field);
            }

            return $field instanceof Field ? [$field] : null;
        });
    }

    /**
     * @return Fields<Field>
     */
    public function tableFields(): Fields
    {
        return $this->onlyFields()->filter(fn(Field $field) => $field->isOnIndex());
    }

    /**
     * @return Fields<Field|Decoration>
     */
    public function formFields(): Fields
    {
        return $this->flatMap(function ($field) {
            if ($field instanceof Tab) {
                $field->fields($field->getFields()->formFields()->toArray());

                return [$field];
            }

            return !method_exists($field, 'isOnForm') || $field->isOnForm()
                ? [$field]
                : null;
        });
    }

    /**
     * @return Fields<Field|Decoration>
     */
    public function detailFields(): Fields
    {
        return $this->flatMap(function ($field) {
            if ($field instanceof Tab) {
                $field->fields($field->getFields()->detailFields()->toArray());

                return [$field];
            }

            return !method_exists($field, 'isOnDetail') || $field->isOnDetail()
                ? [$field]
                : null;
        });
    }

    /**
     * @return Fields<Field>
     */
    public function exportFields(): Fields
    {
        return $this->onlyFields()->filter(fn(Field $field) => $field->isOnExport());
    }

    /**
     * @return array<string, string>
     */
    public function extractLabels(): array
    {
        return $this->onlyFields()->flatMap(function ($field) {
            return [$field->column() => $field->label()];
        })->toArray();
    }

    public function fillValues(Model|array $values): Fields
    {
        return $this->map(function ($field) use ($values) {
            if ($field instanceof Tab) {
                $field->getFields()->each(function ($f) use ($values) {
                    return $f instanceof Field ? $f->resolveFill($values) : $f;
                });
            }

            if ($field instanceof Field) {
                $field = $field->resolveFill($values);
            }

            return $field;
        });
    }

    public function requestValues(): Fields
    {
        return $this->onlyFields()->mapWithKeys(function (Field $field) {
            return [$field->column() => $field->requestValue()];
        })->filter();
    }

    public function onlyFieldsColumns(): Fields
    {
        return $this->onlyFields()->transform(function (Field $field) {
            return $field->column();
        });
    }
}
