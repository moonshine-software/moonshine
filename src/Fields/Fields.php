<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Fields\Fileable;
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
    public function fillCloned(array $raw = [], mixed $casted = null, int $index = 0): self
    {
        return $this->onlyFields()->map(
            fn (Field $field): Field => (clone $field)
                ->resolveFill($raw, $casted, $index)
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

    /**
     * @throws Throwable
     */
    public function requestValues(int|string|null $index = null, ?Closure $column = null): Fields
    {
        return $this->onlyFields()->mapWithKeys(
            fn (Field $field): array => [
                !is_null($column) ? $column($field) : $field->column() => $field->requestValue($index)
            ]
        )->filter();
    }

    /**
     * @throws Throwable
     */
    public function getValues(?Closure $column = null): Fields
    {
        return $this->onlyFields()->mapWithKeys(
            fn (Field $field): array => [
                !is_null($column) ? $column($field) : $field->column() => $field->toValue()
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function reset(): void
    {
        $this->onlyFields()->map(
            fn (Field $field): Field => $field->reset()
        );
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
        return $this->exceptElements(
            fn ($element): bool => $element instanceof ModelRelationField && $element->outsideComponent()
        );
    }

    /**
     * @return Fields<ModelRelationField>
     * @throws Throwable
     */
    public function onlyOutside(): Fields
    {
        return $this->onlyFields()->filter(
            static fn (Field $field): bool => $field instanceof ModelRelationField && $field->outsideComponent()
        )->values();
    }

    /**
     * @return Fields<ModelRelationField>
     * @throws Throwable
     */
    public function withoutRelationFields(): Fields
    {
        return $this->exceptElements(
            fn ($element): bool => $element instanceof ModelRelationField
        );
    }

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function formFields(): Fields
    {
        return $this->exceptElements(
            fn ($element): bool => $element instanceof Field && ! $element->isOnForm()
        );
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
                static fn (Fileable $field): bool => $field->isDeleteFiles() === $isDeleteFiles
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
