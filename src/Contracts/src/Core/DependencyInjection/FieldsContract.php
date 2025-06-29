<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use MoonShine\Contracts\Core\HasStructureContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;

/**
 * @template T of FieldContract = \MoonShine\Contracts\UI\FieldContract
 * @template-extends Enumerable<array-key, T>
 *
 * @mixin Collection<array-key, T>
 */
interface FieldsContract extends Enumerable, HasStructureContract
{
    public function onlyVisible(): static;

    /** @param Closure(ComponentContract): bool $except */
    public function exceptElements(Closure $except): static;

    public function onlyFields(bool $withWrappers = false, bool $withApplyWrappers = false): FieldsContract;

    /**
     * @param  array<string, mixed>  $raw
     * @param  DataWrapperContract<mixed>|null  $casted
     */
    public function fill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): void;

    /**
     * @param  array<string, mixed>  $raw
     * @param  DataWrapperContract<mixed>|null  $casted
     */
    public function fillCloned(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): static;

    /**
     * @param  array<string, mixed>  $raw
     * @param  DataWrapperContract<mixed>|null  $casted
     */
    public function fillClonedRecursively(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): static;

    public function reactiveFields(): static;

    public function refreshFields(): static;

    public function prepareReindexNames(?FieldContract $parent = null, ?callable $before = null, ?callable $performName = null, ?Closure $except = null): static;

    public function prepareAttributes(): static;

    public function whenFieldsConditions(): static;

    /**
     * @param  T  $default
     *
     * @return ?T
     */
    public function findByColumn(
        string $column,
        ?FieldContract $default = null
    ): ?FieldContract;

    /**
     * @param  class-string<T>  $class
     * @param  ?T  $default
     *
     * @return ?T
     */
    public function findByClass(
        string $class,
        ?FieldContract $default = null
    ): ?FieldContract;

    public function wrapNames(string $name): static;

    public function withoutWrappers(bool $applyWrappers = true): static;
}
