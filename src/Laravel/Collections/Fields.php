<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Collections;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\UI\Collections\Fields as ParentFields;
use MoonShine\UI\Fields\Field;
use Throwable;

final class Fields extends ParentFields
{
    /**
     * @throws Throwable
     */
    public function onlyOutside(): self
    {
        return $this->filter(
            static fn (FieldContract $field): bool => $field instanceof ModelRelationField && $field->isOutsideComponent()
        );
    }

    /**
     * @throws Throwable
     */
    public function withoutOutside(): self
    {
        return $this->exceptElements(
            static fn ($element): bool => $element instanceof ModelRelationField && $element->isOutsideComponent()
        );
    }

    /**
     * @throws Throwable
     */
    public function onlyRelationFields(): self
    {
        return $this->filter(
            static fn (FieldContract $field): bool => $field instanceof ModelRelationField
        );
    }

    /**
     * @throws Throwable
     */
    public function withoutRelationFields(): self
    {
        return $this->exceptElements(
            static fn ($element): bool => $element instanceof ModelRelationField
        );
    }

    /**
     * @throws Throwable
     */
    public function indexFields(): self
    {
        return $this->onlyFields(withWrappers: true);
    }

    /**
     * @throws Throwable
     */
    public function formFields(bool $withOutside = true): self
    {
        return $this->when(
            ! $withOutside,
            static fn (self $fields): self => $fields->exceptElements(
                static fn ($element): bool => ($element instanceof ModelRelationField && $element->isOutsideComponent())
            )
        );
    }

    /**
     * @throws Throwable
     */
    public function detailFields(bool $withOutside = false, bool $onlyOutside = false): self
    {
        if ($onlyOutside) {
            return $this->filter(
                static fn (FieldContract $field): bool => $field instanceof ModelRelationField && $field->isOutsideComponent()
            );
        }

        if ($withOutside) {
            return $this;
        }

        return $this->filter(
            static fn (FieldContract $field): bool => ! ($field instanceof ModelRelationField && $field->isOutsideComponent())
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
}
