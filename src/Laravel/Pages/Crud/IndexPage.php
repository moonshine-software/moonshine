<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Buttons\FiltersButton;
use MoonShine\Laravel\Buttons\QueryTagButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Forms\FiltersForm;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Block;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\TableBuilder;
use MoonShine\UI\Contracts\MoonShineRenderable;
use Throwable;

/**
 * @method ModelResource getResource()
 * @extends Page<Fields>
 */
class IndexPage extends Page
{
    protected ?PageType $pageType = PageType::INDEX;

    /**
     * @throws ResourceException
     */
    public function prepareBeforeRender(): void
    {
        abort_if(! $this->getResource()->can('viewAny'), 403);

        parent::prepareBeforeRender();
    }

    /**
     * @return MoonShineComponent
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();

        return $this->getLayers();
    }

    /**
     * @return MoonShineComponent
     */
    protected function topLayer(): array
    {
        $components = [];
        if ($metrics = $this->getMetrics()) {
            $components[] = $metrics;
        }

        return $components;
    }

    /**
     * @return MoonShineComponent
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            $this->hiddenFilters(),
            ...$this->getPageButtons(),
            ...$this->getQueryTags(),
            ...$this->getItemsComponents(),
        ];
    }

    /**
     * @return MoonShineComponent
     */
    protected function bottomLayer(): array
    {
        return $this->getResource()->getIndexPageComponents();
    }

    /**
     * @return FormBuilder<FiltersForm>
     * @throws Throwable
     */
    protected function hiddenFilters(): FormBuilder
    {
        return moonshineConfig()
            ->getForm('filters', FiltersForm::class, resource: $this->getResource())
            ->customAttributes(['class' => 'hidden remove-after-init']);
    }

    protected function getMetrics(): ?MoonShineComponent
    {
        $metrics = $this->getResource()->metrics();

        return $metrics
            ? Block::make($metrics)
                ->customAttributes([
                    'class' => 'flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10 mb-6',
                ])
            : null;
    }

    /*
     * @return list<MoonShineComponent>
     */
    protected function getPageButtons(): array
    {
        return [
            Grid::make([
                Column::make([
                    ActionGroup::make([
                        $this->getResource()->getCreateButton(
                            isAsync: $this->getResource()->isAsync()
                        ),
                        ...$this->getResource()->actions(),
                    ]),

                    ActionGroup::make()->when(
                        $this->getResource()->filters() !== [],
                        fn (ActionGroup $group): ActionGroup => $group->add(
                            FiltersButton::for($this->getResource())
                        )
                    )->when(
                        $this->getResource()->getHandlers()->isNotEmpty(),
                        fn (ActionGroup $group): ActionGroup => $group->addMany(
                            $this->getResource()->getHandlers()->getButtons()
                        )
                    ),
                ])->customAttributes([
                    'class' => 'flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap',
                ]),
            ]),
            LineBreak::make(),
        ];
    }

    /**
     * @return MoonShineComponent
     */
    protected function getQueryTags(): array
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

    public function getListComponentName(): string
    {
        return 'index-table';
    }

    public function getListEventName(): string
    {
        return JsEvent::TABLE_UPDATED->value;
    }

    protected function getItemsComponent(iterable $items, Fields $fields): MoonShineRenderable
    {
        return TableBuilder::make(items: $items)
            ->name($this->getListComponentName())
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
     * @return MoonShineComponent
     * @throws Throwable
     */
    protected function getItemsComponents(): array
    {
        $this->getResource()->setQueryParams(
            request()->only($this->getResource()->getQueryParamsKeys())
        );

        $items = $this->getResource()->isPaginationUsed()
            ? $this->getResource()->paginate()
            : $this->getResource()->items();

        $fields = $this->getResource()->getIndexFields();

        return [
            Fragment::make([
                $this->getItemsComponent($items, $fields),
            ])->name('crud-list'),
        ];
    }
}
