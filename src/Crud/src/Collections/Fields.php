<?php

declare(strict_types=1);

namespace MoonShine\Crud\Collections;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\RelationFieldContract;
use MoonShine\UI\Collections\Fields as BaseFields;
use Throwable;

/**
 * @template T of FieldContract = FieldContract
 * @template TRelation of RelationFieldContract = RelationFieldContract
 * @extends BaseFields<T>
 */
class Fields extends BaseFields
{
    /**
     * @return self<TRelation>
     * @throws Throwable
     */
    public function onlyOutside(): self
    {
        /** @var self<TRelation> */
        return $this->filter(
            static fn (FieldContract $field): bool => $field instanceof RelationFieldContract && $field->isOutsideComponent()
        );
    }

    /**
     * @throws Throwable
     */
    public function withoutOutside(): self
    {
        return $this->exceptElements(
            static fn (ComponentContract $element): bool => $element instanceof RelationFieldContract && $element->isOutsideComponent()
        );
    }

    /**
     * @return self<TRelation>
     * @throws Throwable
     */
    public function onlyRelationFields(): self
    {
        /** @var self<TRelation> */
        return $this->filter(
            static fn (FieldContract $field): bool => $field instanceof RelationFieldContract
        );
    }

    /**
     * @throws Throwable
     */
    public function withoutRelationFields(): self
    {
        return $this->exceptElements(
            static fn (ComponentContract $element): bool => $element instanceof RelationFieldContract
        );
    }

    /**
     *
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
                static fn (ComponentContract $element): bool => ($element instanceof RelationFieldContract && $element->isOutsideComponent())
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
                static fn (FieldContract $field): bool => $field instanceof RelationFieldContract && $field->isOutsideComponent()
            );
        }

        if ($withOutside) {
            return $this;
        }

        return $this->filter(
            static fn (FieldContract $field): bool => ! ($field instanceof RelationFieldContract && $field->isOutsideComponent())
        );
    }
}
