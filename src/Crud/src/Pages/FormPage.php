<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Concerns\Page\HasFormValidation;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Contracts\PageComponents\DefaultFormContract;
use MoonShine\Crud\Pages\PageComponents\DefaultForm;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionGroup;
use Throwable;

/**
 * @template TResource of CrudResource = CrudResource
 * @template TData of mixed = mixed
 * @template TCore of CoreContract = CoreContract
 * @template TFields of Fields = Fields
 *
 * @extends CrudPage<TResource, TCore, TFields>
 * @implements FormPageContract<TResource, TCore, TFields>
 */
class FormPage extends CrudPage implements FormPageContract
{
    /** @use HasFormValidation<TData> */
    use HasFormValidation;

    protected ?PageType $pageType = PageType::FORM;

    /**
     * @var class-string<DefaultFormContract>
     */
    protected string $form = DefaultForm::class;

    public function getTitle(): string
    {
        if ($this->title) {
            return $this->title;
        }

        return $this->getResource()->getItemID()
            ? $this->getCore()->getTranslator()->get('moonshine::ui.edit')
            : $this->getCore()->getTranslator()->get('moonshine::ui.add');
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

        if ($this->getResource()->getItemID()) {
            $breadcrumbs[$this->getRoute()] = data_get(
                $this->getItem(),
                $this->getResource()->getColumn(),
            );
        } else {
            $breadcrumbs[$this->getRoute()] = $this->getCore()->getTranslator()->get('moonshine::ui.add');
        }

        return $breadcrumbs;
    }

    /**
     * @throws ResourceException
     */
    protected function prepareBeforeRender(): void
    {
        $ability = $this->getResource()->getItemID()
            ? Ability::UPDATE
            : Ability::CREATE;

        $action = $this->getResource()->getItemID()
            ? Action::UPDATE
            : Action::CREATE;

        if (
            ! $this->getResource()->hasAction($action) || ! $this->getResource()->can($ability)
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

        if (! $this->isItemExists() && $this->getResource()->getItemID()) {
            $this->throw404();
        }

        return $this->getLayers();
    }

    /**
     * @return list<ComponentContract>
     */
    protected function topLayer(): array
    {
        return $this->getTopButtons();
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            $this->getFormComponent(),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function bottomLayer(): array
    {
        return [];
    }

    protected function getFormAction(): string
    {
        return $this->getResource()->getRoute(
            $this->isItemExists() ? 'crud.update' : 'crud.store',
            $this->getResource()->getItemID(),
        );
    }

    public function getFormComponent(bool $withoutFragment = false): ComponentContract
    {
        $resource = $this->getResource();
        $item = $resource->getCastedData();
        $fields = $this->getResource()->getFormFields();

        $action = $this->getFormAction();

        // Reset form problem
        $isAsync = $this->isAsync();

        if (filter_var($this->getcore()->getRequest()->get('_async_form', false), FILTER_VALIDATE_BOOLEAN)) {
            $isAsync = true;
        }

        $component = $this->getForm(
            $action,
            $item,
            $fields,
            $isAsync,
        );

        if ($withoutFragment) {
            return $component;
        }

        return Fragment::make([$component])
            ->name('crud-form')
            ->updateWith(['resourceItem' => $resource->getItemID()]);
    }

    /**
     * @return null|TData
     */
    protected function getItem(): mixed
    {
        return $this->getResource()->getItem();
    }

    protected function isItemExists(): bool
    {
        return $this->getResource()->isItemExists();
    }

    /**
     * @param  DataWrapperContract<TData>|null  $item
     */
    protected function getForm(
        string $action,
        ?DataWrapperContract $item,
        FieldsContract $fields,
        bool $isAsync = true,
    ): FormBuilderContract {
        $form = $this->getCore()->getContainer($this->form);

        return $this->modifyFormComponent(
            $form(
                $this,
                $action,
                $item,
                $fields,
                $isAsync
            )
        );
    }

    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getTopButtons(): array
    {
        if (! $this->isItemExists()) {
            return [];
        }

        return [
            ActionGroup::make($this->getButtons())
                ->fill($this->getResource()->getCastedData())
                ->class('mb-4'),
        ];
    }

    /**
     * Top form buttons
     *
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->modifyDetailButton(
                $this->getResource()->getDetailButton()
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
     * Form buttons after submit
     *
     * @return ListOf<ActionButtonContract>
     */
    protected function formButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, []);
    }

    public function getFormButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->formButtons()->toArray(),
        )->withoutBulk();
    }

    protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }
}
