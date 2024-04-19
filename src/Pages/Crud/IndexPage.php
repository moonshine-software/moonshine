<?php

declare(strict_types=1);

namespace MoonShine\Pages\Crud;

use MoonShine\Buttons\ExportButton;
use MoonShine\Buttons\FiltersButton;
use MoonShine\Buttons\ImportButton;
use MoonShine\Buttons\QueryTagButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\Layout\LayoutBlock;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Enums\JsEvent;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Fields;
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

    /**
     * @throws ResourceException
     */
    public function beforeRender(): void
    {
        abort_if(! $this->getResource()->can('viewAny'), 403);

        parent::beforeRender();
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();

        return $this->getLayers();
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function topLayer(): array
    {
        $components = [];
        if ($metrics = $this->metrics()) {
            $components[] = $metrics;
        }

        return $components;
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...$this->filtersForm(),
            ...$this->actionButtons(),
            ...$this->queryTags(),
            ...$this->table(),
        ];
    }

    protected function metrics(): ?MoonShineComponent
    {
        $metrics = $this->getResource()->metrics();

        return $metrics
            ? LayoutBlock::make($metrics)
                ->customAttributes([
                    'class' => 'flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10 mb-6',
                ])
            : null;
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function filtersForm(): array
    {
        return [
            Block::make([(new FiltersForm())($this->getResource())])
                ->customAttributes(['class' => 'hidden']),
        ];
    }

    /*
     * @return list<MoonShineComponent>
     */
    protected function actionButtons(): array
    {
        return [
            Grid::make([
                Column::make([
                    ActionGroup::make([
                        $this->getResource()->getCreateButton(),
                        ...$this->getResource()->actions(),
                    ]),

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

    /**
     * @return list<MoonShineComponent>
     */
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

    public function listComponentName(): string
    {
        return 'index-table';
    }

    public function listEventName(): string
    {
        return JsEvent::TABLE_UPDATED->value;
    }

    protected function itemsComponent(iterable $items, Fields $fields): MoonShineRenderable
    {
        return TableBuilder::make(items: $items)
            ->name($this->listComponentName())
            ->fields($fields)
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
            ->buttons($this->getResource()->getIndexItemButtons())
            ->customAttributes([
                'data-click-action' => $this->getResource()->getClickAction(),
            ])
            ->when($this->getResource()->isAsync(), function (TableBuilder $table): void {
                $table->async()->customAttributes([
                    'data-pushstate' => 'true',
                ]);
            });
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function table(): array
    {
        $items = $this->getResource()->isPaginationUsed()
            ? $this->getResource()->paginate()
            : $this->getResource()->items();

        $fields = $this->getResource()->getIndexFields();

        return [
            Fragment::make([
                $this->itemsComponent($items, $fields),
            ])->name('crud-list'),
        ];
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function bottomLayer(): array
    {
        return $this->getResource()->getIndexPageComponents();
    }
}
