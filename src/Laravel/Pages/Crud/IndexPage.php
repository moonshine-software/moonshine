<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Buttons\QueryTagButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Layout\Block;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\Table\TableBuilder;
use Throwable;

/**
 * @method ModelResource getResource()
 * @extends Page<Fields>
 */
class IndexPage extends Page
{
    protected ?PageType $pageType = PageType::INDEX;

    public function getTitle(): string
    {
        return $this->title ?: $this->getResource()->getTitle();
    }

    /**
     * @throws ResourceException
     */
    public function prepareBeforeRender(): void
    {
        abort_if(! $this->getResource()->can(Ability::VIEW_ANY), 403);

        parent::prepareBeforeRender();
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function components(): array
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
        if ($metrics = $this->getMetrics()) {
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
            ...$this->getPageButtons(),
            ...$this->getQueryTags(),
            ...$this->getItemsComponents(),
        ];
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function bottomLayer(): array
    {
        return $this->getResource()->getIndexPageComponents();
    }

    protected function getMetrics(): ?MoonShineComponent
    {
        if($this->getResource()->isListComponentRequest()) {
            return null;
        }

        $metrics = $this->getResource()->getMetrics();

        return $metrics
            ? Block::make($metrics)
                ->class('layout-metrics')
            : null;
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function getPageButtons(): array
    {
        return [
            Flex::make([
                ActionGroup::make(
                    $this->getResource()->getTopButtons(),
                ),

                ActionGroup::make()->when(
                    $this->getResource()->hasFilters(),
                    fn (ActionGroup $group): ActionGroup => $group->add(
                        $this->getResource()->getFiltersButton()
                    )
                )->when(
                    $this->getResource()->getHandlers()->isNotEmpty(),
                    fn (ActionGroup $group): ActionGroup => $group->addMany(
                        $this->getResource()->getHandlers()->getButtons()
                    )
                ),
            ])
                ->justifyAlign('between')
                ->itemsAlign('start'),
            LineBreak::make(),
        ];
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function getQueryTags(): array
    {
        return [
            ActionGroup::make()->when(
                $this->getResource()->hasQueryTags(),
                function (ActionGroup $group): ActionGroup {
                    foreach ($this->getResource()->getQueryTags() as $tag) {
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
        return "index-table-{$this->getResource()->getUriKey()}";
    }

    public function getListEventName(): string
    {
        return JsEvent::TABLE_UPDATED->value;
    }

    protected function getItemsComponent(iterable $items, Fields $fields): RenderableContract
    {
        return TableBuilder::make(items: $items)
            ->name($this->getListComponentName())
            ->fields($fields)
            ->cast($this->getResource()->getModelCast())
            ->withNotFound()
            ->when(
                ! is_null($this->getResource()->getTrAttributes()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->trAttributes(
                    $this->getResource()->getTrAttributes()
                )
            )
            ->when(
                ! is_null($this->getResource()->getTdAttributes()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->tdAttributes(
                    $this->getResource()->getTdAttributes()
                )
            )
            ->buttons($this->getResource()->getIndexButtons())
            ->customAttributes([
                'data-click-action' => $this->getResource()->getClickAction(),
            ])
            ->when(
                ! is_null($this->getResource()->getClickAction()),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->tdAttributes(
                    static fn (): array => [
                        '@click.stop' => 'rowClickAction',
                    ]
                )
            )
            ->when($this->getResource()->isAsync(), static function (TableBuilderContract $table): void {
                $table->async()->customAttributes([
                    'data-pushstate' => 'true',
                ]);
            })
            ->when($this->getResource()->isStickyTable(), function (TableBuilderContract $table): void {
                $table->sticky();
            })
            ->when($this->getResource()->isColumnSelection(), function (TableBuilderContract $table): void {
                $table->columnSelection();
            });
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function getItemsComponents(): array
    {
        $this->getResource()->setQueryParams(
            request()->only($this->getResource()->getQueryParamsKeys())
        );

        $items = $this->getResource()->isPaginationUsed()
            ? $this->getResource()->paginate()
            : $this->getResource()->getItems();

        $fields = $this->getResource()->getIndexFields();

        return [
            Fragment::make([
                $this->getItemsComponent($items, $fields),
            ])->name('crud-list'),
        ];
    }
}
