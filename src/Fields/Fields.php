<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\Decorations\FieldsDecoration;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\HasPivot;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Decorations\Tabs;
use Leeto\MoonShine\Exceptions\FieldException;
use Leeto\MoonShine\Exceptions\FieldsException;
use ReflectionClass;
use ReflectionException;
use Throwable;

final class Fields extends Collection
{
    /**
     * @throws Throwable
     */
    private function withdrawFields($fieldsOrDecorations, array &$fields): void
    {
        foreach ($fieldsOrDecorations as $fieldOrDecoration) {
            if ($fieldOrDecoration instanceof Field) {
                $fields[] = $fieldOrDecoration;
            } elseif ($fieldOrDecoration instanceof Tabs) {
                foreach ($fieldOrDecoration->tabs() as $tab) {
                    $this->withdrawFields($tab->getFields(), $fields);
                }
            } elseif ($fieldOrDecoration instanceof Decoration) {
                $this->withdrawFields($fieldOrDecoration->getFields(), $fields);
            }
        }
    }

    public function resolveChildFields(Field $parent): Fields
    {
        return $this->map(function (Field $field) use ($parent) {
            throw_if(
                $parent instanceof Json && $field->hasRelationship(),
                new FieldException('Relationship fields in JSON field unavailable. Use resourceMode')
            );

            throw_if(
                ! $field instanceof Json && $field instanceof HasFields,
                new FieldException('Field with fields unavailable. Use resourceMode')
            );

            if ($parent instanceof HasPivot) {
                return $field->setName("{$parent->relation()}_{$field->field()}[]");
            }

            return $field->setName(
                (string)str($parent->name())
                    ->when(
                        $parent->hasFields(),
                        fn (Stringable $s) => $s->append('[${index'.$s->substrCount('$').'}]')
                    )
                    ->append("[{$field->field()}]")
                    ->replace('[]', '')
                    ->when($field->getAttribute('multiple'), fn (Stringable $s) => $s->append('[]'))
            )->xModel();
        });
    }

    public function withParents(): Fields
    {
        return $this->map(static function ($fieldsOrDecoration) {
            if ($fieldsOrDecoration instanceof Field) {
                return $fieldsOrDecoration->setParents();
            }

            return $fieldsOrDecoration;
        });
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function onlyFields(): Fields
    {
        $fieldsOrDecorations = [];

        $this->withdrawFields($this->toArray(), $fieldsOrDecorations);

        return self::make($fieldsOrDecorations);
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function whenFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field) => $field->hasShowWhen())
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function whenFieldNames(): Fields
    {
        return $this->whenFields()->mapWithKeys(static function (Field $field) {
            return [$field->showWhenField] = $field->showWhenField;
        });
    }

    /**
     * @throws Throwable
     */
    public function isWhenConditionField(string $name): bool
    {
        return $this->whenFieldNames()->has($name);
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function indexFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field) => $field->isOnIndex())
            ->values();
    }


    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function relatable(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field) => $field->isResourceModeField())
            ->values()
            ->map(fn (Field $field) => $field->setParents());
    }

    /**
     * @return Fields<Field|Decoration>
     * @throws Throwable
     */
    public function formFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field) => $field->isOnForm())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function detailFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field) => $field->isOnDetail())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function exportFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field) => $field->isOnExport())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function importFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field) => $field->isOnImport())
            ->values();
    }

    /**
     * @return array<string, string>
     * @throws Throwable
     */
    public function extractLabels(): array
    {
        return $this->onlyFields()->flatMap(static function ($field) {
            return [$field->field() => $field->label()];
        })->toArray();
    }


    /**
     * @param  string  $resource
     * @param  ?Field  $default
     * @return ?Field
     * @throws Throwable
     */
    public function findFieldByResourceClass(string $resource, Field $default = null): ?Field
    {
        return $this->onlyFields()->first(static function (Field $field) use ($resource) {
            return $field->resource()
                && $field->resource()::class === $resource;
        }, $default);
    }

    /**
     * @param  string  $relation
     * @param  ?Field  $default
     * @return ?Field
     * @throws Throwable
     */
    public function findFieldByRelation(string $relation, Field $default = null): ?Field
    {
        return $this->onlyFields()->first(static function (Field $field) use ($relation) {
            return $field->relation() === $relation;
        }, $default);
    }

    /**
     * @param  string  $column
     * @param  ?Field  $default
     * @return ?Field
     * @throws Throwable
     */
    public function findFieldByColumn(string $column, Field $default = null): ?Field
    {
        return $this->onlyFields()->first(static function (Field $field) use ($column) {
            return $field->field() === $column;
        }, $default);
    }

    /**
     * @throws Throwable
     */
    public function onlyFieldsColumns(): Fields
    {
        return $this->onlyFields()->transform(static function (Field $field) {
            return $field->field();
        });
    }

    /**
     * @throws ReflectionException|FieldsException
     */
    public function wrapIntoDecoration(string $class, string $label): Fields
    {
        $reflectionClass = new ReflectionClass($class);

        if (! $reflectionClass->implementsInterface(FieldsDecoration::class)) {
            throw FieldsException::wrapError();
        }

        return self::make([new $class($label, $this->toArray())]);
    }
}
