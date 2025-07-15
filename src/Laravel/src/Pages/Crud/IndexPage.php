<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Pages\IndexPage as CrudIndexPage;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Applies\FieldsWithoutFilters;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Traits\Page\OverrideCrudPage;

/**
 * @template TResource of CrudResource = \MoonShine\Laravel\Resources\ModelResource
 *
 * @extends CrudIndexPage<TResource, MoonShine, Fields>
 */
class IndexPage extends CrudIndexPage
{
    use OverrideCrudPage;

    /**
     * @return list<class-string<FieldContract>>
     */
    protected function getIgnoredFields(): array
    {
        return FieldsWithoutFilters::LIST;
    }
}
