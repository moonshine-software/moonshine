<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ActionsLayer;

use Leeto\MoonShine\Contracts\EntityContract;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\ViewComponents\Table\Table;

final class MakeTableAction
{
    public function __invoke(ResourceContract $resource): Table
    {
        $entitiesPaginator = $resource->paginate()->through(
            function ($values) use ($resource): array|EntityContract {
                if (!method_exists($resource, 'entity')) {
                    return $values;
                }

                return $resource->entity($values);
            }
        );

        return Table::make(
            $entitiesPaginator,
            $resource->fieldsCollection()->tableFields(),
        );
    }
}
