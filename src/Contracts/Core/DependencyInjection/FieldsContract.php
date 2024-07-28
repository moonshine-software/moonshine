<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\FieldContract;
use Traversable;

/**
 * @template-covariant T
 */
interface FieldsContract extends Traversable
{
    /**
     * @return T
     */
    public function onlyFields(bool $withWrappers = false): static;

    public function fill(array $raw = [], ?CastedDataContract $casted = null, int $index = 0): void;

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
