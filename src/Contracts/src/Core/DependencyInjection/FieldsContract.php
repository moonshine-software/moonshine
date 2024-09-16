<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use MoonShine\Contracts\Core\HasStructureContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;

/**
 * @template T of FieldContract
 * @template-extends Enumerable<array-key, T>
 *
 * @mixin Collection
 */
interface FieldsContract extends Enumerable, HasStructureContract
{
    public function onlyVisible(): static;

    public function exceptElements(Closure $except): static;

    public function onlyFields(bool $withWrappers = false): FieldsContract;

    public function fill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): void;

    public function fillCloned(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): static;

    public function fillClonedRecursively(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): static;

    public function reactiveFields(): static;

    public function prepareReindexNames(?FieldContract $parent = null, ?callable $before = null, ?callable $performName = null): static;

    public function prepareAttributes(): static;

    public function whenFieldsConditions(): static;

    /**
     * @param  T  $default
     *
     * @return ?T
     */
    public function findByColumn(
        string $column,
        FieldContract $default = null
    ): ?FieldContract;

    /**
     * @param  class-string<T>  $class
     * @param  ?T  $default
     *
     * @return ?T
     */
    public function findByClass(
        string $class,
        FieldContract $default = null
    ): ?FieldContract;

    public function wrapNames(string $name): static;

    public function withoutWrappers(): static;
}
