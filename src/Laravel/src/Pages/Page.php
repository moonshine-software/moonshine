<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Crud\Pages\Page as CrudPage;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Traits\Page\OverrideCrudPage;

/**
 * @template TResource of CrudResourceContract = CrudResourceContract
 * @extends CrudPage<TResource, MoonShine>
 */
abstract class Page extends CrudPage
{
    use OverrideCrudPage;
}
