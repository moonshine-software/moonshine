<?php

namespace MoonShine\Laravel\Pages\Crud;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Contracts\MoonShineRenderable;
use MoonShine\UI\Fields\Hidden;
use Throwable;

/**
 * @method ModelResource getResource()
 * @extends Page<Fields>
 */
class FormPage extends Page
{
    protected ?PageType $pageType = PageType::FORM;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (! is_null($this->breadcrumbs)) {
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
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();
        $item = $this->getResource()->getItem();

        if (! $item?->exists && $this->getResource()->getItemID()) {
            oops404();
        }

        return $this->getLayers();
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function topLayer(): array
    {
        return $this->getPageButtons();
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        $resource = $this->getResource();
        $item = $resource->getItem();

        $action = $resource->getRoute(
            $item?->exists ? 'crud.update' : 'crud.store',
            $item?->getKey()
        );

        // Reset form problem
        $isAsync = $resource->isAsync();

        if (request()->boolean('_async_form')) {
            $isAsync = true;
        }

        return $this->getFormComponents($action, $item, $isAsync);
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        $components = [];
        $item = $this->getResource()->getItem();

        if (! $item?->exists) {
            return $components;
        }

        $outsideFields = $this->getResource()->getOutsideFields()->formFields();

        if ($outsideFields->isNotEmpty()) {
            $components[] = Divider::make();

            /** @var ModelRelationField $field */
            foreach ($outsideFields as $field) {
                $components[] = LineBreak::make();

                $components[] = Fragment::make([
                    Heading::make($field->getLabel()),

                    $field->fillCast(
                        $item,
                        $field->getResource()?->getModelCast()
                    ),
                ])->name($field->getRelationName());
            }
        }

        return array_merge($components, $this->getResource()->getFormPageComponents());
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function getPageButtons(): array
    {
        $item = $this->getResource()->getItem();

        if(! $item?->exists) {
            return [];
        }

        return [
            ActionGroup::make($this->getResource()->getFormItemButtons())
                ->fill($this->getResource()->getCastedItem())
                ->customAttributes(['class' => 'mb-4']),
        ];
    }

    /**
     * @throws Throwable
     * @return list<MoonShineComponent>
     */
    protected function getFormComponents(
        string $action,
        ?Model $item,
        bool $isAsync = true,
    ): array {
        $resource = $this->getResource();

        return [
            Fragment::make([
                $this->getFormComponent(
                    $action,
                    $item,
                    $this->getResource()->getFormFields(),
                    $isAsync
                ),
            ])
                ->name('crud-form')
                ->updateWith(['resourceItem' => $resource->getItemID()]),
        ];
    }

    /**
     * @return MoonShineComponent
     */
    protected function getFormComponent(
        string $action,
        ?Model $item,
        Fields $fields,
        bool $isAsync = true,
    ): MoonShineRenderable {
        $resource = $this->getResource();

        return FormBuilder::make($action)
            ->fillCast(
                $item,
                $resource->getModelCast()
            )
            ->fields(
                $fields
                    ->when(
                        ! is_null($item),
                        static fn (Fields $fields): Fields => $fields->push(
                            Hidden::make('_method')->setValue('PUT')
                        )
                    )
                    ->when(
                        ! $item?->exists && ! $resource->isCreateInModal(),
                        static fn (Fields $fields): Fields => $fields->push(
                            Hidden::make('_force_redirect')->setValue(true)
                        )
                    )
                    ->toArray()
            )
            ->when(
                $isAsync,
                static fn (FormBuilder $formBuilder): FormBuilder => $formBuilder
                    ->async(events: array_filter([
                        $resource->getListEventName(
                            request()->input('_component_name', 'default')
                        ),
                        ! $item?->exists && $resource->isCreateInModal()
                            ? AlpineJs::event(JsEvent::FORM_RESET, $resource->getUriKey())
                            : null,
                    ]))
            )
            ->when(
                $resource->isPrecognitive() || (moonshineRequest()->isFragmentLoad('crud-form') && ! $isAsync),
                static fn (FormBuilder $form): FormBuilder => $form->precognitive()
            )
            ->name($resource->getUriKey())
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
            ->buttons($resource->getFormBuilderButtons());
    }
}
