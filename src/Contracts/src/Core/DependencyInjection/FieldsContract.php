<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use Traversable;

/**
 * @template-covariant T
 *
 * @template-implements Traversable<array-key, FieldContract>
 */
interface FieldsContract extends Traversable
{
    /**
     * @return T
     */
    public function onlyFields(bool $withWrappers = false): static;

    public function fill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): void;

    /**
     * @return T
     */
    public function reactiveFields(): static;

    public function findByColumn(
        string $column,
        FieldContract $default = null
    ): ?FieldContract;

    public function findByClass(
        string $class,
        FieldContract $default = null
    ): ?FieldContract;
}
