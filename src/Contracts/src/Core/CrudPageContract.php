<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\ComponentContract;

/**
 * @template TResource of CrudResourceContract = CrudResourceContract
 * @template TCore of CoreContract = CoreContract
 * @template TFields of FieldsContract = FieldsContract
 *
 * @extends PageContract<TCore, TResource>
 */
interface CrudPageContract extends PageContract
{
    /**
     * @return TFields
     */
    public function getFields(): FieldsContract;

    public function isAsync(): bool;

    /**
     * @return list<ComponentContract>
     */
    public function getEmptyModals(): array;

    public function getButtons(): ActionButtonsContract;
}
