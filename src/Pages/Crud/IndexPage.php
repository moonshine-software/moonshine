<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use MoonShine\Buttons\CreateButton;
use MoonShine\Buttons\DeleteButton;
use MoonShine\Buttons\DetailButton;
use MoonShine\Buttons\EditButton;
use MoonShine\Buttons\ExportButton;
use MoonShine\Buttons\FiltersButton;
use MoonShine\Buttons\ImportButton;
use MoonShine\Buttons\MassDeleteButton;
use MoonShine\Buttons\QueryTagButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\Layout\LayoutBlock;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Enums\PageType;
use MoonShine\Forms\FiltersForm;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use Throwable;

/**
 * @method ModelResource getResource()
 */
class IndexPage extends Page
{
    protected ?PageType $pageType = PageType::INDEX;

    public function beforeRender(): void
    {
        abort_if(! $this->getResource()->can('viewAny'), 403);
    }

    /**
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();

        return $this->getLayers();
    }

    protected function topLayer(): array
    {
        $components = [];
        if($metrics = $this->metrics()) {
            $components[] = $metrics;
        }

        return $components;
    }

    /**
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [...$this->filtersForm(), ...$this->actionButtons(), ...$this->queryTags(), ...$this->table()];
    }

    protected function metrics(): ?MoonshineComponent
    {
        $metrics = $this->getResource()->metrics();

        return $metrics
            ? LayoutBlock::make($metrics)
                ->customAttributes([
                    'class' => 'flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10 mb-6',
                ])
            : null
        ;
    }

    protected function filtersForm(): array
    {
        return [
            Block::make([(new FiltersForm())($this->getResource())])
                ->customAttributes(['class' => 'hidden']),
        ];
    }

    protected function actionButtons(): array
    {
        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ActionGroup::make([
                            CreateButton::for($this->getResource(), 'index-table'),
                            ...$this->getResource()->actions(),
                        ]),
                    ])->justifyAlign('start'),

                    ActionGroup::make()->when(
                        $this->getResource()->filters() !== [],
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            FiltersButton::for($this->getResource())
                        )
                    )->when(
                        ! is_null($export = $this->getResource()->export()),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ExportButton::for($this->getResource(), $export)
                        ),
                    )->when(
                        ! is_null($import = $this->getResource()->import()),
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            ImportButton::for($this->getResource(), $import)
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
        return [
            ActionGroup::make()->when(
                $this->getResource()->queryTags() !== [],
                function (ActionGroup $group): ActionGroup {
                    foreach ($this->getResource()->queryTags() as $tag) {
                        $group->add(
                            QueryTagButton::for($this->getResource(), $tag)
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
        $tableName = 'index-table';

        return [
            Fragment::make([
                TableBuilder::make(items: $this->getResource()->paginate())
                    ->fields(fn () => $this->getResource()->getIndexFields()->toArray())
                    ->cast($this->getResource()->getModelCast())
                    ->withNotFound()
                    ->when(
                        ! is_null($this->getResource()->trAttributes()),
                        fn (TableBuilder $table): TableBuilder => $table->trAttributes(
                            $this->getResource()->trAttributes()
                        )
                    )
                    ->when(
                        ! is_null($this->getResource()->tdAttributes()),
                        fn (TableBuilder $table): TableBuilder => $table->tdAttributes(
                            $this->getResource()->tdAttributes()
                        )
                    )
                    ->when($this->getResource()->isAsync(), function (TableBuilder $table): void {
                        $table->async(tableAsyncRoute())
                            ->customAttributes([
                                'data-pushstate' => 'true',
                            ]);
                    })
                    ->buttons([
                        ...$this->getResource()->getIndexButtons(),
                        DetailButton::for($this->getResource()),
                        EditButton::for($this->getResource(), $tableName),
                        DeleteButton::for($this->getResource(), $tableName),
                        MassDeleteButton::for($this->getResource(), $tableName),
                    ])
                    ->customAttributes([
                        'data-click-action' => $this->getResource()->getClickAction(),
                    ])
                    ->name($tableName),
            ])->name('crud-table'),
        ];
    }
}
