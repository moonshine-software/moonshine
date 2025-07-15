<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Crud\Collections\Fields;

/**
 * @template TFields of Fields = Fields
 */
interface HasFiltersContract
{
    public function hasFilters(): bool;

    /**
     * @return TFields
     */
    public function getFilters(): FieldsContract;

    /**
     * @return array<array-key, mixed>
     */
    public function getFilterParams(): array;
}
