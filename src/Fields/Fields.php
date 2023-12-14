<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Contracts\Fields\HasFields;
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
    public function onlyRelationFields(): Fields
    {
        return $this->filter(
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

    public function withoutHasFields(): Fields
    {
        return $this->filter(static fn (Field $field): bool => $field instanceof HasFields);
    }

    /**
     * @throws Throwable
     */
    public function onlyOutside(): Fields
    {
        return $this->filter(
            static fn (Field $field): bool => $field instanceof ModelRelationField && $field->outsideComponent()
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
    public function indexFields(): Fields
    {
        return $this
            ->filter(static fn (Field $field): bool => $field->isOnIndex())
            ->values();
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
        return $this
            ->filter(static fn (Field $field): bool => $field->isOnDetail())
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function exportFields(): Fields
    {
        return $this->filter(static fn (Field $field): bool => $field->isOnExport())
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function importFields(): Fields
    {
        return $this->filter(static fn (Field $field): bool => $field->isOnImport())
            ->values();
    }

    /**
     * @return array<string, string>
     * @throws Throwable
     */
    public function extractLabels(): array
    {
        return $this->flatMap(
            static fn (Field $field): array => [$field->column() => $field->label()]
        )->toArray();
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
        return $this->first(
            static fn (Field $field): bool => $field->column() === $column,
            $default
        );
    }
}
