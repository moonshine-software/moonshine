<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Concerns\Page\HasFilters;
use MoonShine\Crud\Concerns\Page\HasHandlers;
use MoonShine\Crud\Concerns\Page\HasListComponent;
use MoonShine\Crud\Concerns\Page\HasMetrics;
use MoonShine\Crud\Concerns\Page\HasQueryTags;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Contracts\PageComponents\DefaultListComponentContract;
use MoonShine\Crud\Pages\PageComponents\DefaultListComponent;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use Throwable;

/**
 * @template TResource of CrudResource = CrudResource
 * @template TCore of CoreContract = CoreContract
 * @template TFields of Fields = Fields
 *
 * @extends CrudPage<TResource, TCore, TFields>
 * @implements IndexPageContract<TResource, TCore, TFields>
 */
class IndexPage extends CrudPage implements IndexPageContract
{
    use HasHandlers;
    use HasQueryTags;
    /** @use HasFilters<TFields> */
    use HasFilters;
    use HasListComponent;
    use HasMetrics;

    protected ?PageType $pageType = PageType::INDEX;

    /**
     * @var class-string<DefaultListComponentContract>
     */
    protected string $component = DefaultListComponent::class;

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

    /**
     * @param  TFields  $fields
     *
     * @return TFields
     */
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
        if (! $this->getResource()->can(Ability::VIEW_ANY)) {
            $this->throw403();
        }

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

    /**
     * @param  iterable<array-key, mixed>  $items
     * @param  TFields  $fields
     */
    protected function getItemsComponent(iterable $items, FieldsContract $fields): ComponentContract
    {
        $component = $this->getCore()->getContainer($this->component);

        return $this->modifyListComponent(
            $component($this, $items, $fields)
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
        if ($this->getCore()->getRequest()->has('_no_items_query')) {
            return [];
        }

        $this->getResource()->setQueryParams(
            $this->getCore()->getRequest()->getOnly($this->getResource()->getQueryParamsKeys()),
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
            )->class('flex-wrap'),
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
