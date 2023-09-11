<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use MoonShine\Buttons\IndexPage\CreateButton;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\DetailButton;
use MoonShine\Buttons\IndexPage\ExportButton;
use MoonShine\Buttons\IndexPage\FiltersButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Buttons\IndexPage\ImportButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Buttons\IndexPage\QueryTagButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\Layout\LayoutBlock;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Decoration;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Forms\FiltersForm;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use Throwable;

class IndexPage extends Page
{
    /**
     * @throws Throwable
     */
    public function components(): array
    {
        $resource = $this->getResource();

        $components = $this->beforeComponents();

        if($metrics = $this->metrics($resource)) {
            $components[] = $metrics;
        }

        return array_merge($components, [
            ...$this->filtersForm($resource),

            ...$this->actionButtons($resource),

            ...$this->queryTags($resource),

            $this->table($resource),

            ...$this->afterComponents(),
        ]);
    }

    protected function metrics(ModelResource $resource): ?MoonshineComponent
    {
        $metrics = $resource->metrics();

        return $metrics
            ? LayoutBlock::make($metrics)
                ->customAttributes(['class' => 'flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10 mb-6'])
            : null
        ;
    }

    protected function filtersForm(ModelResource $resource): array
    {
        return [
            Block::make([(new FiltersForm())($resource)])
                ->customAttributes(['class' => 'hidden']),
        ];
    }

    protected function actionButtons(ModelResource $resource): array
    {
        $export = $resource->export();
        $import = $resource->import();

        return [
            Grid::make([
                Column::make([
                    Flex::make([CreateButton::forMode($resource)])->justifyAlign('start'),

                    ActionGroup::make()->when(
                        $resource->filters() !== [],
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
        ];
    }

    protected function queryTags(ModelResource $resource): array
    {
        return [
            ActionGroup::make()->when(
                $resource->queryTags() !== [],
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
        ];
    }

    /**
     * @throws Throwable
     */
    protected function table(ModelResource $resource): Decoration
    {
        $items = $resource->paginate();

        return Fragment::make([
            TableBuilder::make(items: $items)
                ->fields($resource->getIndexFields())
                ->cast($resource->getModelCast())
                ->withNotFound()
                ->buttons([
                    ...$resource->getIndexButtons(),
                    DetailButton::forMode($resource),
                    FormButton::forMode($resource),
                    DeleteButton::for($resource),
                    MassDeleteButton::for($resource),
                ]),
        ])->withName('crud-table');
    }
}
