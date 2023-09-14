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
use MoonShine\Contracts\Resources\ResourceContract;
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
     * @var ResourceContract|ModelResource|null
     */
    protected ResourceContract|ModelResource|null $resource = null;

    /**
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();

        return array_merge(
            $this->topLayer(),

            $this->filtersForm(),

            $this->actionButtons(),

            $this->queryTags(),

            $this->table(),

            $this->bottomLayer(),
        );
    }

    protected function topLayer(): array
    {
        $componetns = [];
        if($metrics = $this->metrics()) {
            $componetns[] = $metrics;
        }
        return $componetns;
    }

    protected function metrics(): ?MoonshineComponent
    {
        $metrics = $this->resource->metrics();

        return $metrics
            ? LayoutBlock::make($metrics)
                ->customAttributes(['class' => 'flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10 mb-6'])
            : null
        ;
    }

    protected function filtersForm(): array
    {
        return [
            Block::make([(new FiltersForm())($this->resource)])
                ->customAttributes(['class' => 'hidden']),
        ];
    }

    protected function actionButtons(): array
    {
        return [
            Grid::make([
                Column::make([
                    Flex::make([CreateButton::forMode($this->resource)])->justifyAlign('start'),

                    ActionGroup::make()->when(
                        $this->resource->filters() !== [],
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            FiltersButton::for($this->resource)
                        )
                    )->when(
                        ! is_null($export = $this->resource->export()),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ExportButton::for($this->resource, $export)
                        ),
                    )->when(
                        ! is_null($import = $this->resource->import()),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ImportButton::for($this->resource, $import)
                        ),
                    ),
                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),
            LineBreak::make(),
        ];
    }

    protected function queryTags(): array
    {
        $resource = $this->resource;

        return [
            ActionGroup::make()->when(
                $this->resource->queryTags() !== [],
                function (ActionGroup $group) use ($resource): ActionGroup {
                    foreach ($resource->queryTags() as $tag) {
                        $group->add(
                            QueryTagButton::for($this->resource, $tag)
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
    protected function table(): array
    {
        return [
            Fragment::make([
                TableBuilder::make(items: $this->resource->paginate())
                    ->fields($this->resource->getIndexFields())
                    ->cast($this->resource->getModelCast())
                    ->withNotFound()
                    ->when(
                        ! is_null($this->resource->trAttributes()),
                        fn (TableBuilder $table): TableBuilder => $table->trAttributes($this->resource->trAttributes())
                    )
                    ->when(
                        ! is_null($this->resource->tdAttributes()),
                        fn (TableBuilder $table): TableBuilder => $table->tdAttributes($this->resource->tdAttributes())
                    )
                    ->buttons([
                        ...$this->resource->getIndexButtons(),
                        DetailButton::forMode($this->resource),
                        FormButton::forMode($this->resource),
                        DeleteButton::for($this->resource),
                        MassDeleteButton::for($this->resource),
                    ]),
            ])->withName('crud-table')
        ];
    }
}
