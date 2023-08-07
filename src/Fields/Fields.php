<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Relationships\ModelRelationField;
use Throwable;

/**
 * @template TKey of array-key
 * @template Field
 *
 * @extends  Collection<TKey, Field>
 */
final class Fields extends FormElements
{
    /**
     * @throws Throwable
     */
    public function fillCloned(array $raw = [], mixed $casted = null): self
    {
        return $this->onlyFields()->map(
            fn (Field $field): Field => (clone $field)
                ->resolveFill($raw, $casted)
        );
    }

    /**
     * @throws Throwable
     */
    public function fill(array $raw = [], mixed $casted = null): void
    {
        $this->onlyFields()->map(
            fn (Field $field): Field => $field
                ->resolveFill($raw, $casted)
        );
    }

    public function requestValues(string $prefix = null): Fields
    {
        return $this->onlyFields()->mapWithKeys(
            fn (Field $field): array => [$field->column() => $field->requestValue($prefix)]
        )->filter();
    }

    public function getValues(): Fields
    {
        return $this->onlyFields()->mapWithKeys(
            fn (Field $field): array => [$field->column() => $field->value()]
        )->filter();
    }

    public function reset(): void
    {
        $this->onlyFields()->map(
            fn (Field $field): Field => $field->reset()
        );
    }

    /**
     * @throws Throwable
     */
    public function resolveSiblings(Field $parent): Fields
    {
        return $this->map(function (Field $field) use ($parent): Field|NoInput {
            throw_if(
                $parent instanceof Json && $field instanceof ModelRelationField,
                new FieldException(
                    'Relationship fields in JSON field unavailable'
                )
            );

            if ($parent instanceof HasPivot) {
                return $field->setName(
                    "{$parent->getRelationName()}_{$field->column()}[]"
                );
            }

            return $field->setName(
                (string) str($parent->name())
                    ->when(
                        $parent->hasFields(),
                        fn (Stringable $s): Stringable => $s->append(
                            '[${index' . $s->substrCount('$') . '}]'
                        )
                    )
                    ->append("[{$field->column()}]")
                    ->replace('[]', '')
                    ->when(
                        $field->getAttribute('multiple'),
                        static fn (Stringable $s): Stringable => $s->append(
                            '[]'
                        )
                    )
            )->xModel();
        });
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function indexFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnIndex())
            ->values();
    }


    /**
     * @return Fields<ModelRelationField>
     * @throws Throwable
     */
    public function onlyRelationFields(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field instanceof ModelRelationField
            )
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function withoutOutside(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field instanceof ModelRelationField && !$field->outsideComponent()
            )
            ->values();
    }

    /**
     * @return Fields<ModelRelationField>
     * @throws Throwable
     */
    public function onlyOutside(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field instanceof ModelRelationField && $field->outsideComponent()
            )
            ->values();
    }

    /**
     * @return Fields<ModelRelationField>
     * @throws Throwable
     */
    public function withoutRelationFields(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => ! $field instanceof ModelRelationField
            )
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function formFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnForm())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function detailFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnDetail())
            ->values();
    }

    /**
     * @return Fields<File>
     *
     * @throws Throwable
     */
    public function onlyDeletableFileFields(bool $isDeleteFiles = true): Fields
    {
        return $this->onlyFileFields()
            ->filter(
                static fn (Field $field): bool => $field->isDeleteFiles() === $isDeleteFiles
            )
            ->values();
    }

    /**
     * @return Fields<File>
     *
     * @throws Throwable
     */
    public function onlyFileFields(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field instanceof Fileable
            )
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function exportFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnExport())
            ->values();
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function importFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnImport())
            ->values();
    }
}
