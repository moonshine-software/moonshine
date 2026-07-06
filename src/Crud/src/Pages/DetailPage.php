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
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Contracts\Page\DetailPageContract;
use MoonShine\Crud\Contracts\PageComponents\DefaultDetailComponentContract;
use MoonShine\Crud\Pages\PageComponents\DefaultDetailComponent;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use Throwable;

/**
 * @template TResource of CrudResource = CrudResource
 * @template TCore of CoreContract = CoreContract
 * @template TFields of Fields = Fields
 *
 * @extends CrudPage<TResource, TCore, TFields>
 * @implements  DetailPageContract<TResource, TCore, TFields>
 */
class DetailPage extends CrudPage implements DetailPageContract
{
    protected ?PageType $pageType = PageType::DETAIL;

    /**
     * @var class-string<DefaultDetailComponentContract>
     */
    protected string $component = DefaultDetailComponent::class;

    public function getTitle(): string
    {
        return $this->title ?: $this->getCore()->getTranslator()->get('moonshine::ui.show');
    }

    /**
     * @param  TFields  $fields
     *
     * @return TFields
     */
    protected function prepareFields(FieldsContract $fields): FieldsContract
    {
        /** @var TFields */
        return $fields->ensure([FieldsWrapperContract::class, FieldContract::class]);
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (! \is_null($this->breadcrumbs)) {
            return $this->breadcrumbs;
        }

        $breadcrumbs = parent::getBreadcrumbs();

        $breadcrumbs[$this->getRoute()] = data_get($this->getItem(), $this->getResource()->getColumn());

        return $breadcrumbs;
    }

    /**
     * @throws ResourceException
     */
    protected function prepareBeforeRender(): void
    {
        if (
            ! $this->getResource()->hasAction(Action::VIEW)
            || ! $this->getResource()->can(Ability::VIEW)
        ) {
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

        if (! $this->isItemExists()) {
            $this->throw404();
        }

        return $this->getLayers();
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            Box::make([
                $this->getDetailComponent(),
                LineBreak::make(),
                ...$this->getTopButtons(),
            ]),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function bottomLayer(): array
    {
        return [];
    }

    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    protected function getItem(): mixed
    {
        return $this->getResource()->getItem();
    }

    protected function isItemExists(): bool
    {
        return $this->getResource()->isItemExists();
    }

    public function getDetailComponent(bool $withoutFragment = false): ComponentContract
    {
        $resource = $this->getResource();

        $detailComponent = $this->getCore()->getContainer(
            $this->component
        );

        $component = $this->modifyDetailComponent(
            $detailComponent($this, $resource->getCastedData(), $resource->getDetailFields())
        );

        if ($withoutFragment) {
            return $component;
        }

        return Fragment::make([$component])->name('crud-detail');
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->modifyEditButton(
                $this->getResource()->getEditButton(
                    isAsync: $this->isAsync(),
                )
            ),
            $this->modifyDeleteButton(
                $this->getResource()->getDeleteButton(
                    redirectAfterDelete: $this->getResource()->getRedirectAfterDelete(),
                    isAsync: false,
                )
            ),
        ]);
    }

    public function getButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->buttons()->toArray(),
        )->withoutBulk();
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getTopButtons(): array
    {
        return [
            ActionGroup::make(
                $this->getButtons(),
            )
                ->fill($this->getResource()->getCastedData())
                ->class('justify-end'),
        ];
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }
}
