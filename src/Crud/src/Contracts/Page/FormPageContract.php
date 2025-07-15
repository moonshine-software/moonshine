<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Page;

use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Contracts\HasFormValidationContract;

/**
 * @template TResource of CrudResourceContract = \MoonShine\Crud\Resources\CrudResource
 * @template TCore of CoreContract = CoreContract
 * @template TFields of Fields = Fields
 *
 * @extends CrudPageContract<TResource, TCore, TFields>
 */
interface FormPageContract extends
    CrudPageContract,
    HasFormValidationContract
{
    public function getFormComponent(bool $withoutFragment = false): ComponentContract;

    public function getFormButtons(): ActionButtonsContract;
}
