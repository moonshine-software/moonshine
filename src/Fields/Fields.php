<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Decorations\Tab;

class Fields extends Collection
{
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

    public function fillValues(Model $values): Fields
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

    public function performValues(Model $values): Fields
    {
        return $this->map(function ($field) use ($values) {
            if ($field instanceof Tab) {
                $field->getFields()->each(function ($f) use ($values) {
                    return $f instanceof Field ? $f->performValue($values) : $f;
                });
            }

            if ($field instanceof Field) {
                $field = $field->performValue($values);
            }

            return $field;
        });
    }
}
