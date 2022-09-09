<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ActionsLayer;

use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\ViewComponents\Table\Table;

final class MakeTableAction
{
    public function __invoke(ResourceContract $resource): Table
    {
        $entitiesPaginator = $resource->paginate()->through(function ($values) use ($resource) {
            if (!method_exists($resource, 'valueEntity')) {
                return $values;
            }

            return $resource->valueEntity($values);
        });

        return Table::make(
            $entitiesPaginator,
            $resource->fieldsCollection()->tableFields(),
        );
    }
}
