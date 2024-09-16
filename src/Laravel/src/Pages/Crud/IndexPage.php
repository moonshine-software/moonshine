<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Buttons\QueryTagButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Contracts\Resource\HasQueryTagsContract;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Layout\Block;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Table\TableBuilder;
use Throwable;

class IndexPage extends CrudPage
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
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function components(): iterable
    {
        $this->validateResource();

        return $this->getLayers();
    }

    /**
     * @return list<ComponentContract>
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
     * @return list<ComponentContract>
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
     * @return list<ComponentContract>
     */
    protected function bottomLayer(): array
    {
        return $this->getResource()->getIndexPageComponents();
    }

    protected function getMetrics(): ?ComponentContract
    {
        if ($this->getResource()->isListComponentRequest()) {
            return null;
        }

        $metrics = $this->getResource()->getMetrics();

        return $metrics
            ? Block::make($metrics)
                ->class('layout-metrics')
            : null;
    }

    /**
     * @return list<ComponentContract>
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
     * @return list<ComponentContract>
     */
    protected function getQueryTags(): array
    {
        $resource = $this->getResource();

        if (! $resource instanceof HasQueryTagsContract) {
            return [];
        }

        return [
            ActionGroup::make()->when(
                $resource->hasQueryTags(),
                static function (ActionGroup $group) use ($resource): ActionGroup {
                    foreach ($resource->getQueryTags() as $tag) {
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

    public function getListComponentName(): string
    {
        return "index-table-{$this->getResource()->getUriKey()}";
    }

    public function getListEventName(): string
    {
        return JsEvent::TABLE_UPDATED->value;
    }

    protected function getItemsComponent(iterable $items, Fields $fields): ComponentContract
    {
        return TableBuilder::make(items: $items)
            ->name($this->getListComponentName())
            ->fields($fields)
            ->cast($this->getResource()->getCaster())
            ->withNotFound()
            ->when(
                ! is_null($head = $this->getResource()->getHeadRows()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->headRows($head)
            )
            ->when(
                ! is_null($body = $this->getResource()->getRows()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->rows($body)
            )
            ->when(
                ! is_null($foot = $this->getResource()->getFootRows()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->footRows($foot)
            )
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
            ->clickAction($this->getResource()->getClickAction())
            ->when($this->getResource()->isAsync(), static function (TableBuilderContract $table): void {
                $table->async()->pushState();
            })
            ->when($this->getResource()->isStickyTable(), function (TableBuilderContract $table): void {
                $table->sticky();
            })
            ->when($this->getResource()->isColumnSelection(), function (TableBuilderContract $table): void {
                $table->columnSelection();
            });
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function getItemsComponents(): array
    {
        $this->getResource()->setQueryParams(
            request()->only($this->getResource()->getQueryParamsKeys())
        );

        $items = $this->getResource()->getItems();
        $fields = $this->getResource()->getIndexFields();

        return [
            Fragment::make([
                $this->getResource()->modifyListComponent(
                    $this->getItemsComponent($items, $fields)
                ),
            ])->name('crud-list'),
        ];
    }
}
