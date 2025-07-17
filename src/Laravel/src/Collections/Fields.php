<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Collections;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Collections\Fields as BaseFields;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use Throwable;

/**
 * @template T of FieldContract = FieldContract
 * @extends BaseFields<T, ModelRelationField>
 */
final class Fields extends BaseFields
{
    /**
     * @throws Throwable
     */
    public function findByRelation(
        string $relation,
        ?ModelRelationField $default = null
    ): ?ModelRelationField {
        return $this->onlyRelationFields()->first(
            static fn (ModelRelationField $field): bool => $field->getRelationName() === $relation,
            $default
        );
    }
}
