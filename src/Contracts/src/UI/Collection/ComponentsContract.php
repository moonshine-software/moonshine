<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI\Collection;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\HasStructureContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\TableBuilderContract;

/**
 * @template T of ComponentContract
 * @template-extends Enumerable<array-key, T>
 *
 * @mixin Collection
 */
interface ComponentsContract extends Enumerable, HasStructureContract
{
    public function onlyVisible(): static;

    public function exceptElements(Closure $except): static;

    public function onlyFields(bool $withWrappers = false): FieldsContract;

    public function onlyForms(): static;

    public function onlyTables(): static;

    public function onlyComponents(): static;

    public function findForm(
        string $name,
        FormBuilderContract $default = null
    ): ?FormBuilderContract;

    public function findTable(
        string $name,
        TableBuilderContract $default = null
    ): ?TableBuilderContract;

    public function findByName(
        string $name,
        ComponentContract $default = null
    ): ?ComponentContract;
}
