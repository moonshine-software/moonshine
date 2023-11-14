<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Fields\Relationships\ModelRelationField;
use Throwable;

/**
 * @extends FormElements<int, Field>
 */
final class Fields extends FormElements
{
    /**
     * @throws Throwable
     */
    public function fill(array $raw = [], mixed $casted = null, int $index = 0): void
    {
        $this->onlyFields()->map(
            fn (Field $field): Field => $field
                ->resolveFill($raw, $casted, $index)
        );
    }

    /**
     * @throws Throwable
     */
    public function requestValues(int|string|null $index = null, ?Closure $column = null): Fields
    {
        return $this->onlyFields()->mapWithKeys(
            fn (Field $field): array => [
                is_null($column) ? $field->column() : $column($field) => $field->requestValue($index),
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
                is_null($column) ? $field->column() : $column($field) => $field->toValue(),
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function wrapNames(string $name): Fields
    {
        $this
            ->onlyFields()
            ->each(fn (Field $field): Field => $field->wrapName($name));

        return $this;
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
     * @throws Throwable
     */
    public function indexFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnIndex())
            ->values();
    }


    /**
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
     * @throws Throwable
     */
    public function withoutOutside(): MoonShineRenderElements
    {
        return $this->exceptElements(
            fn ($element): bool => $element instanceof ModelRelationField && $element->outsideComponent()
        );
    }

    /**
     * @throws Throwable
     */
    public function onlyOutside(): Fields
    {
        return $this->onlyFields()->filter(
            static fn (Field $field): bool => $field instanceof ModelRelationField && $field->outsideComponent()
        )->values();
    }

    /**
     * @throws Throwable
     */
    public function onlyGrouped(): Fields
    {
        return $this->onlyFields()->filter(
            static fn (Field $field): bool => $field->isGroup()
        )->values();
    }

    /**
     * @throws Throwable
     */
    public function withoutRelationFields(): MoonShineRenderElements
    {
        return $this->exceptElements(
            fn ($element): bool => $element instanceof ModelRelationField
        );
    }

    /**
     * @throws Throwable
     */
    public function formFields(): MoonShineRenderElements
    {
        return $this->exceptElements(
            fn ($element): bool => $element instanceof Field && ! $element->isOnForm()
        );
    }

    /**
     * @throws Throwable
     */
    public function detailFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnDetail())
            ->values();
    }

    /**
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
     * @throws Throwable
     */
    public function exportFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnExport())
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function importFields(): Fields
    {
        return $this->onlyFields()
            ->filter(static fn (Field $field): bool => $field->isOnImport())
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function isWhenConditionField(string $name): bool
    {
        return $this->whenFieldNames()->has($name);
    }

    /**
     * @throws Throwable
     */
    public function whenFieldNames(): Fields
    {
        return $this->whenFields()->mapWithKeys(
            static fn (Field $field): array => [
                $field->showWhenCondition()['changeField'] => $field->showWhenCondition()['changeField'],
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function whenFields(): Fields
    {
        return $this->onlyFields()
            ->filter(
                static fn (Field $field): bool => $field->hasShowWhen()
            )
            ->values();
    }

    public function withoutForeignField(): MoonShineRenderElements
    {
        return $this->exceptElements(
            fn (mixed $field): bool => $field instanceof ModelRelationField
                && $field->toOne()
                && $field->getResource()->uriKey() === moonshineRequest()->getResourceUri()
        );
    }

    /**
     * @return array<string, string>
     * @throws Throwable
     */
    public function extractLabels(): array
    {
        return $this->onlyFields()->flatMap(
            static fn (Field $field): array => [$field->column() => $field->label()]
        )->toArray();
    }


    /**
     * @throws Throwable
     */
    public function findByResourceClass(
        string $resource,
        ModelRelationField $default = null
    ): ?ModelRelationField {
        return $this->onlyRelationFields()->first(
            static fn (ModelRelationField $field): bool => $field->getResource()::class === $resource,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findByRelation(
        string $relation,
        ModelRelationField $default = null
    ): ?ModelRelationField {
        return $this->onlyRelationFields()->first(
            static fn (ModelRelationField $field): bool => $field->getRelationName() === $relation,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findByColumn(
        string $column,
        Field $default = null
    ): ?Field {
        return $this->onlyFields()->first(
            static fn (Field $field): bool => $field->column() === $column,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function onlyColumns(): Fields
    {
        return $this->onlyFields()->transform(
            static fn (Field $field): string => $field->column()
        );
    }
}
