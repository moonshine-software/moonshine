<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

/**
 * @template TRelated of mixed = mixed
 *
 */
interface RelationFieldContract extends FieldContract
{
    public function getRelationName(): string;

    public function getResourceColumn(): string;

    /**
     * @return TRelated
     */
    public function getRelated(): mixed;

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $relations
     * @param  null|TRelated  $related
     *
     * @return null|TRelated
     */
    public function makeRelated(
        int|string|null $key = null,
        array $attributes = [],
        array $relations = [],
        mixed $related = null,
    ): mixed;

    public function excludeInstancing(): void;

    public function isOutsideComponent(): bool;

    public function isToOne(): bool;

    public function isMorph(): bool;
}
