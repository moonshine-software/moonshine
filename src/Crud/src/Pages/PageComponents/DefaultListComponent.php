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
        $resource = $page->getResource();

        return TableBuilder::make(items: $items)
            ->name($page->getListComponentName())
            ->queryParamPrefix($resource->getQueryParamPrefix())
            ->fields($fields)
            ->cast($resource->getCaster())
            ->withNotFound()
            ->buttons($page->getButtons())
            ->when($page->isAsync(), function (TableBuilderContract $table) use ($page): void {
                $table->async(
                    url: fn (): string
                        => $page->getRouter()->getEndpoints()->component(
                            name: $table->getName(),
                            additionally: $this->getCore()->getRequest()->getRequest()->getQueryParams(),
                        ),
                )->pushState();
            })
            ->when($page->isLazy(), function (TableBuilderContract $table) use ($resource): void {
                $table->lazy()->whenAsync(
                    fn (TableBuilderContract $t): TableBuilderContract
                        => $t->items(
                            $resource->getItems(),
                        ),
                );
            })
            ->when(
                ! \is_null($resource->getItemsResolver()),
                function (TableBuilderContract $table) use ($resource): void {
                    $table->itemsResolver(
                        $resource->getItemsResolver(),
                    );
                },
            );
    }
}
