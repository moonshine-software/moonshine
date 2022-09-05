<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ActionsLayer;

use Leeto\MoonShine\Contracts\ResourceContract;
use Leeto\MoonShine\ValueEntities\ModelValueEntityBuilder;
use Leeto\MoonShine\ViewComponents\Table\Table;

final class MakeTableAction
{
    public function __invoke(ResourceContract $resource): Table
    {
        $entitiesPaginator = $resource->paginate()
            ->getCollection()
            ->transform(function ($values) use ($resource) {
                # TODO switch ValueEntityBuilder by resource
                return (new ModelValueEntityBuilder($values))
                    ->build()
                    ->withActions($resource->rowActions($values));
            });

        return Table::make(
            $entitiesPaginator,
            $resource->fieldsCollection()->tableFields(),
        );
    }
}
