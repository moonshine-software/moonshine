<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Concerns\Page\HasFormValidation;
use MoonShine\Laravel\Contracts\Fields\HasModalModeContract;
use MoonShine\Laravel\Contracts\Fields\HasTabModeContract;
use MoonShine\Laravel\Contracts\Page\FormPageContract;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Resources\CrudResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Hidden;
use Throwable;

/**
 * @template TResource of CrudResource = \MoonShine\Laravel\Resources\ModelResource
 * @template TData of mixed = \Illuminate\Database\Eloquent\Model
 * @extends CrudPage<TResource>
 */
class FormPage extends CrudPage implements FormPageContract
{
    /** @use HasFormValidation<TData> */
    use HasFormValidation;

    protected ?PageType $pageType = PageType::FORM;

    public function getTitle(): string
    {
        if ($this->title) {
            return $this->title;
        }

        return $this->getResource()->getItemID()
            ? __('moonshine::ui.edit')
            : __('moonshine::ui.add');
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
            $breadcrumbs[$this->getRoute()] = data_get($this->getResource()->getItem(), $this->getResource()->getColumn());
        } else {
            $breadcrumbs[$this->getRoute()] = __('moonshine::ui.add');
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

        abort_if(
            ! $this->getResource()->hasAction($action) || ! $this->getResource()->can($ability),
            403
        );

        parent::prepareBeforeRender();
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function components(): iterable
    {
        $this->validateResource();

        if (! $this->getResource()->isItemExists() && $this->getResource()->getItemID()) {
            oops404();
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
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        $components = [];
        $item = $this->getResource()->getItem();

        if (! $this->getResource()->isItemExists()) {
            return $components;
        }

        $outsideFields = $this->getResource()->getOutsideFields()->formFields();

        if ($outsideFields->isEmpty()) {
            return array_merge($components, $this->getEmptyModals());
        }

        $tabs = [];

        $components[] = Divider::make();

        /** @var ModelRelationField $field */
        foreach ($outsideFields as $field) {

            $components[] = LineBreak::make();

            $fieldComponent = $field instanceof HasModalModeContract && $field->isModalMode()
                // With the modalMode, the field is already inside the fragment
                ? $field->fillCast(
                    $item,
                    $field->getResource()?->getCaster()
                )
                : Fragment::make([
                    Heading::make($field->getLabel()),

                    $field->fillCast(
                        $item,
                        $field->getResource()?->getCaster()
                    ),
                ])->name($field->getRelationName());

            if ($field instanceof HasTabModeContract && $field->isTabMode()) {
                $tabs[] = Tab::make($field->getLabel(), [
                    $fieldComponent,
                ]);

                continue;
            }

            $components[] = $fieldComponent;
        }

        if ($tabs !== []) {
            $components[] = Tabs::make($tabs);
        }

        return array_merge($components, $this->getEmptyModals());
    }

    public function getFormComponent(bool $withoutFragment = false): ComponentContract
    {
        $resource = $this->getResource();
        $item = $resource->getCastedData();
        $fields = $this->getResource()->getFormFields();

        $action = $resource->getRoute(
            $resource->isItemExists() ? 'crud.update' : 'crud.store',
            $item?->getKey()
        );

        // Reset form problem
        $isAsync = $this->isAsync();

        if (request()->boolean('_async_form')) {
            $isAsync = true;
        }

        $component = $this->getForm(
            $action,
            $item,
            $fields,
            $isAsync
        );

        if ($withoutFragment) {
            return $component;
        }

        return Fragment::make([$component])
            ->name('crud-form')
            ->updateWith(['resourceItem' => $resource->getItemID()]);
    }

    /**
     * @param  string  $action
     * @param  DataWrapperContract<TData>|null  $item
     *
     */
    protected function getForm(
        string $action,
        ?DataWrapperContract $item,
        Fields $fields,
        bool $isAsync = true,
    ): FormBuilderContract {
        $resource = $this->getResource();

        return $this->modifyFormComponent(
            FormBuilder::make($action)
                ->cast($this->getResource()->getCaster())
                ->fill($item)
                ->fields([
                    ...$fields
                        ->when(
                            ! \is_null($item),
                            static fn (Fields $fields): Fields => $fields->push(
                                Hidden::make('_method')->setValue('PUT')
                            )
                        )
                        ->toArray(),
                ])
                ->when(
                    ! $this->hasErrorsAbove(),
                    fn (FormBuilderContract $form): FormBuilderContract => $form->errorsAbove($this->hasErrorsAbove())
                )
                ->when(
                    $isAsync,
                    static fn (FormBuilderContract $formBuilder): FormBuilderContract => $formBuilder
                        ->async(events: array_filter([
                            $resource->getListEventName(
                                request()->getScalar('_component_name', 'default'),
                                $isAsync && $resource->isItemExists() ? array_filter([
                                    'page' => request()->getScalar('page'),
                                    'sort' => request()->getScalar('sort'),
                                ]) : []
                            ),
                            ! $resource->isItemExists() && $resource->isCreateInModal()
                                ? AlpineJs::event(JsEvent::FORM_RESET, $resource->getUriKey())
                                : null,
                        ]))
                )
                ->when(
                    $this->isPrecognitive() || (moonshineRequest()->isFragmentLoad('crud-form') && ! $isAsync),
                    static fn (FormBuilderContract $form): FormBuilderContract => $form->precognitive()
                )
                ->name($resource->getUriKey())
                ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
                ->buttons($this->getFormButtons())
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
        if (! $this->getResource()->isItemExists()) {
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
            $this->getResource()->getDetailButton(),
            $this->getResource()->getDeleteButton(
                redirectAfterDelete: $this->getResource()->getRedirectAfterDelete(),
                isAsync: false
            ),
        ]);
    }

    public function getButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->buttons()->toArray()
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
            $this->formButtons()->toArray()
        )->withoutBulk();
    }
}
