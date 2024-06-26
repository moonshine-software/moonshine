<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Buttons\FiltersButton;
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
        abort_if(! $this->getResource()->can(Ability::VIEW_ANY), 403);

        parent::prepareBeforeRender();
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
        $metrics = $this->getResource()->metrics();

        return $metrics
            ? Block::make($metrics)
                ->customAttributes([
                    'class' => 'layout-metrics',
                ])
            : null;
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function getPageButtons(): array
    {
        return [
            Flex::make([
                ActionGroup::make([
                    $this->getResource()->getCreateButton(
                        isAsync: $this->getResource()->isAsync()
                    ),
                    ...$this->getResource()->topButtons(),
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
            ->when(
                ! is_null($this->getResource()->getClickAction()),
                static fn (TableBuilder $table): TableBuilder => $table->tdAttributes(
                    static fn (): array => [
                        '@click.stop' => 'rowClickAction',
                    ]
                )
            )
            ->when($this->getResource()->isAsync(), static function (TableBuilder $table): void {
                $table->async()->customAttributes([
                    'data-pushstate' => 'true',
                ]);
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
