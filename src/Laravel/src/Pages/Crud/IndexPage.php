<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Buttons\FiltersButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Concerns\Page\HasFilters;
use MoonShine\Laravel\Concerns\Page\HasHandlers;
use MoonShine\Laravel\Concerns\Page\HasListComponent;
use MoonShine\Laravel\Concerns\Page\HasMetrics;
use MoonShine\Laravel\Concerns\Page\HasQueryTags;
use MoonShine\Laravel\Contracts\Page\IndexPageContract;
use MoonShine\Laravel\Resources\CrudResource;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use Throwable;

/**
 * @template TResource of CrudResource = \MoonShine\Laravel\Resources\ModelResource
 * @extends CrudPage<TResource>
 */
class IndexPage extends CrudPage implements IndexPageContract
{
    use HasHandlers;
    use HasQueryTags;
    use HasFilters;
    use HasListComponent;
    use HasMetrics;

    protected ?PageType $pageType = PageType::INDEX;

    protected bool $isLazy = false;

    protected bool $queryTagsInDropdown = false;

    protected bool $buttonsInDropdown = false;

    public function getTitle(): string
    {
        return $this->title ?: $this->getResource()->getTitle();
    }

    public function isLazy(): bool
    {
        return $this->isLazy;
    }

    public function isQueryTagsInDropdown(): bool
    {
        return $this->queryTagsInDropdown;
    }

    public function isButtonsInDropdown(): bool
    {
        return $this->buttonsInDropdown;
    }

    protected function prepareFields(FieldsContract $fields): FieldsContract
    {
        /** @var Fields $fields */
        return $fields->ensure([FieldContract::class, FieldsWrapperContract::class]);
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

        if ($metrics = $this->getMetricsComponent()) {
            $components[] = $metrics;
        }

        return array_merge($components, $this->getTopButtons());
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...$this->getQueryTagsButtons(),
            ...$this->getItemsComponents(),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function bottomLayer(): array
    {
        return [
            ...$this->getEmptyModals(),
        ];
    }

    protected function getMetricsComponent(): ?ComponentContract
    {
        if ($this->getResource()->isListComponentRequest()) {
            return null;
        }

        $components = Div::make($this->getMetrics())->class('layout-metrics');


        if (! \is_null($fragment = $this->getFragmentMetrics())) {
            return $fragment([$components]);
        }

        return $components;
    }

    protected function getItemsComponent(iterable $items, Fields $fields): ComponentContract
    {
        return $this->modifyListComponent(
            TableBuilder::make(items: $items)
                ->name($this->getListComponentName())
                ->fields($fields)
                ->cast($this->getResource()->getCaster())
                ->withNotFound()
                ->buttons($this->getButtons())
                ->when($this->isAsync(), function (TableBuilderContract $table): void {
                    $table->async(
                        url: fn (): string
                            => $this->getRouter()->getEndpoints()->component(
                                name: $table->getName(),
                                additionally: request()->query(),
                            ),
                    )->pushState();
                })
                ->when($this->isLazy(), function (TableBuilderContract $table): void {
                    $table->lazy()->whenAsync(
                        fn (TableBuilderContract $t): TableBuilderContract
                            => $t->items(
                                $this->getResource()->getItems(),
                            ),
                    );
                })
                ->when(
                    ! \is_null($this->getResource()->getItemsResolver()),
                    function (TableBuilderContract $table): void {
                        $table->itemsResolver(
                            $this->getResource()->getItemsResolver(),
                        );
                    },
                ),
        );
    }

    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function getItemsComponents(): array
    {
        if (request()->has('_no_items_query')) {
            return [];
        }

        $this->getResource()->setQueryParams(
            request()->only($this->getResource()->getQueryParamsKeys()),
        );

        return [
            $this->getListComponent(),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getTopButtons(): array
    {
        return [
            Flex::make([
                ActionGroup::make(
                    $this->getTopLeftButtons(),
                ),

                ActionGroup::make(
                    $this->getTopRightButtons(),
                ),
            ])
                ->justifyAlign($this->getTopLeftButtons()->onlyVisible()->isEmpty() ? 'end' : 'between')
                ->itemsAlign('start'),
            LineBreak::make(),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function topLeftButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->modifyCreateButton(
                $this->getResource()->getCreateButton(
                    isAsync: $this->isAsync()
                )
            ),
        ]);
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function topRightButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, array_filter([
            $this->getResource()->hasFilters() ? $this->getFiltersButton() : null,
            ...$this->getResource()->getHandlers()->getButtons()->toArray(),
        ]));
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->modifyDetailButton(
                $this->getResource()->getDetailButton()
            ),
            $this->modifyEditButton(
                $this->getResource()->getEditButton(
                    isAsync: $this->isAsync(),
                )
            ),
            $this->modifyDeleteButton(
                $this->getResource()->getDeleteButton(
                    redirectAfterDelete: $this->getResource()->getRedirectAfterDelete(),
                    isAsync: $this->isAsync(),
                )
            ),
            $this->modifyMassDeleteButton(
                $this->getResource()->getMassDeleteButton(
                    redirectAfterDelete: $this->getResource()->getRedirectAfterDelete(),
                    isAsync: $this->isAsync(),
                )
            ),
        ]);
    }

    protected function getTopLeftButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->topLeftButtons()->toArray());
    }

    protected function getTopRightButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->topRightButtons()->toArray());
    }

    public function getButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->buttons()->toArray(),
        )->when(
            $this->isButtonsInDropdown(),
            fn (ActionButtonsContract $buttons)
                => $buttons->map(
                    fn (ActionButtonContract $button): ActionButtonContract => $button->showInDropdown(),
                ),
        );
    }

    protected function getFiltersButton(): ActionButtonContract
    {
        return $this->modifyFiltersButton(
            $this->getResource()->getFiltersButton(),
        );
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getQueryTagsButtons(): array
    {
        $resource = $this->getResource();

        return [
            ActionGroup::make()->when(
                $resource->hasQueryTags(),
                function (ActionGroup $group) use ($resource): ActionGroup {
                    foreach ($resource->getQueryTags() as $tag) {
                        $group->add(
                            $tag->getButton($this),
                        );
                    }

                    return $group;
                },
            )->customAttributes(['class' => 'flex-wrap']),
            LineBreak::make(),
        ];
    }

    protected function modifyCreateButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyFiltersButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyMassDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }
}
