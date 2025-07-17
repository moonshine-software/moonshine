<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Page;

use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Collections\Fields;

/**
 * @template TResource of CrudResourceContract = \MoonShine\Crud\Resources\CrudResource
 * @template TCore of CoreContract = CoreContract
 * @template TFields of Fields = Fields
 *
 * @extends CrudPageContract<TResource, TCore, TFields>
 */
interface DetailPageContract extends CrudPageContract
{
    public function getDetailComponent(bool $withoutFragment = false): ComponentContract;
}
