<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Page;

use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\HasListComponentContract;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Contracts\HasFiltersContract;
use MoonShine\Crud\Contracts\HasHandlersContract;
use MoonShine\Crud\Contracts\HasMetricsContract;
use MoonShine\Crud\Contracts\HasQueryTagsContract;

/**
 * @template TResource of CrudResourceContract = \MoonShine\Crud\Resources\CrudResource
 * @template TCore of CoreContract = CoreContract
 * @template TFields of Fields = Fields
 *
 * @extends CrudPageContract<TResource, TCore, TFields>
 * @extends HasFiltersContract<TFields>
 */
interface IndexPageContract extends
    CrudPageContract,
    HasQueryTagsContract,
    HasHandlersContract,
    HasFiltersContract,
    HasListComponentContract,
    HasMetricsContract
{
    public function isLazy(): bool;

    public function isQueryTagsInDropdown(): bool;

    public function isButtonsInDropdown(): bool;
}
