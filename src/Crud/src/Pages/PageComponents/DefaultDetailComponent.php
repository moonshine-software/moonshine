<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages\PageComponents;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Contracts\Page\DetailPageContract;
use MoonShine\Crud\Contracts\PageComponents\DefaultDetailComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;

final class DefaultDetailComponent implements DefaultDetailComponentContract
{
    public function __invoke(
        DetailPageContract $page,
        ?DataWrapperContract $item,
        FieldsContract $fields,
    ): ComponentContract {
        $resource = $page->getResource();

        return TableBuilder::make($fields)
            ->cast($resource->getCaster())
            ->items([$item])
            ->vertical(
                title: $resource->isDetailInModal() ? 3 : 2,
                value: $resource->isDetailInModal() ? 9 : 10,
            )
            ->simple()
            ->preview()
            ->class('table-divider');
    }
}
