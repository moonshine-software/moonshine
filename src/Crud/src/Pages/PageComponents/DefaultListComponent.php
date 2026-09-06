<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages\PageComponents;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Contracts\PageComponents\DefaultListComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;

final class DefaultListComponent implements DefaultListComponentContract
{
    use WithCore;

    public function __construct(CoreContract $core)
    {
        $this->setCore($core);
    }

    /**
     * @param  iterable<array-key, mixed>  $items
     */
    public function __invoke(
        IndexPageContract $page,
        iterable $items,
        FieldsContract $fields
    ): ComponentContract {
        $resource = $page->getResourceOrFail();

        return TableBuilder::make(items: $items)
            ->name($page->getListComponentName())
            ->queryParamPrefix($resource->getQueryParamPrefix())
            ->fields($fields)
            ->cast($resource->getCaster())
            ->buttons($page->getButtons())
            ->when($page->isAsync(), function (TableBuilderContract $table) use ($page): void {
                /** @var array<string, mixed> $queryParams */
                $queryParams = $this->getCore()->getRequest()->getRequest()->getQueryParams();

                $table->async(
                    url: fn (): string
                        => $page->getRouter()->getEndpoints()->component(
                            name: $table->getName(),
                            additionally: $queryParams,
                        ),
                )->pushState();
            })
            ->when(
                $page->isLazy(),
                function (TableBuilderContract $table) use ($resource): void {
                    $table->lazy()->whenAsync(
                        fn (TableBuilderContract $t): TableBuilderContract
                            => $t->items(
                                $resource->getItems(),
                            )->withNotFound(),
                    );
                },
                fn (TableBuilderContract $table): TableBuilder => $table->withNotFound()
            )
            ->when(
                ! \is_null($resource->getItemsResolver()),
                function (TableBuilderContract $table) use ($resource): void {
                    if (($resolver = $resource->getItemsResolver()) !== null) {
                        $table->itemsResolver($resolver);
                    }
                },
            );
    }
}
