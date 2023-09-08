<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use MoonShine\Buttons\IndexPage\AsyncCreateButton;
use MoonShine\Buttons\IndexPage\CreateButton;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\ExportButton;
use MoonShine\Buttons\IndexPage\FiltersButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Buttons\IndexPage\ImportButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Buttons\IndexPage\QueryTagButton;
use MoonShine\Buttons\IndexPage\ShowButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Forms\FiltersForm;
use MoonShine\Pages\Page;

class IndexPage extends Page
{
    public function components(): array
    {
        $resource = $this->getResource();

        $items = $resource->paginate();

        $export = $resource->export();
        $import = $resource->import();

        return [
            Block::make([
                (new FiltersForm())($resource),
            ])->customAttributes(['class' => 'hidden']),

            Grid::make([
                Column::make([
                    Flex::make([
                        AsyncCreateButton::for($resource),
                        CreateButton::for($resource),
                    ])->justifyAlign('start'),

                    ActionGroup::make()->when(
                        ! empty($resource->filters()),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            FiltersButton::for($resource)
                        )
                    )->when(
                        ! is_null($export),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ExportButton::for($resource, $export)
                        ),
                    )->when(
                        ! is_null($import),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ImportButton::for($resource, $import)
                        ),
                    ),
                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),

            LineBreak::make(),

            ActionGroup::make()->when(
                ! empty($resource->queryTags()),
                function (ActionGroup $group) use ($resource): ActionGroup {
                    foreach ($resource->queryTags() as $tag) {
                        $group->add(
                            QueryTagButton::for($resource, $tag)
                        );
                    }

                    return $group;
                }
            ),

            LineBreak::make(),

            Fragment::make([
                TableBuilder::make(items: $items)
                    ->fields($resource->getIndexFields())
                    ->cast($resource->getModelCast())
                    ->withNotFound()
                    ->buttons([
                        ShowButton::for($resource),
                        FormButton::for($resource),
                        DeleteButton::for($resource),
                        MassDeleteButton::for($resource),
                        ...$resource->buttons()
                    ]),
            ])->withName('crud-table'),
        ];
    }
}
