<?php

namespace MoonShine\Pages\Crud;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\LineBreak;
use MoonShine\Enums\JsEvent;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use Throwable;

/**
 * @method ModelResource getResource()
 */
class FormPage extends Page
{
    protected ?PageType $pageType = PageType::FORM;

    /**
     * @return array<string, string>
     */
    public function breadcrumbs(): array
    {
        if (! is_null($this->breadcrumbs)) {
            return $this->breadcrumbs;
        }

        $breadcrumbs = parent::breadcrumbs();

        if ($this->getResource()->getItemID()) {
            $breadcrumbs[$this->route()] = data_get($this->getResource()->getItem(), $this->getResource()->column());
        } else {
            $breadcrumbs[$this->route()] = __('moonshine::ui.add');
        }

        return $breadcrumbs;
    }

    /**
     * @throws ResourceException
     */
    public function beforeRender(): void
    {
        $ability = $this->getResource()->isNowOnUpdateForm()
            ? 'update'
            : 'create';

        abort_if(
            ! in_array(
                $ability,
                $this->getResource()->getActiveActions()
            )
            || ! $this->getResource()->can($ability),
            403
        );

        parent::beforeRender();
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();
        $item = $this->getResource()->getItem();

        if (
            ! $item?->exists
            && $this->getResource()->isNowOnUpdateForm()
        ) {
            oops404();
        }

        return $this->getLayers();
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function topLayer(): array
    {
        $components = [];
        $item = $this->getResource()->getItem();

        if ($item?->exists) {
            $components[] = ActionGroup::make($this->getResource()->getFormItemButtons())
                ->setItem($item)
                ->customAttributes(['class' => 'mb-4']);
        }

        return $components;
    }

    protected function formComponent(
        string $action,
        ?Model $item,
        Fields $fields,
        bool $isAsync = false,
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
                        fn (Fields $fields): Fields => $fields->push(
                            Hidden::make('_method')->setValue('PUT')
                        )
                    )
                    ->when(
                        ! $item?->exists && ! $resource->isCreateInModal(),
                        fn (Fields $fields): Fields => $fields->push(
                            Hidden::make('_force_redirect')->setValue(true)
                        )
                    )
                    ->toArray()
            )
            ->when(
                $isAsync,
                fn (FormBuilder $formBuilder): FormBuilder => $formBuilder
                    ->async(asyncEvents: [
                        $resource->listEventName(
                            request()->input('_component_name', 'default'),
                            $isAsync && $item?->exists ? array_filter([
                                'page' => request()->input('page'),
                                'sort' => request()->input('sort'),
                            ]) : []
                        ),
                        ! $item?->exists && $resource->isCreateInModal()
                            ? AlpineJs::event(JsEvent::FORM_RESET, $resource->uriKey())
                            : null,
                    ])
            )
            ->when(
                $resource->isPrecognitive() || (moonshineRequest()->isFragmentLoad('crud-form') && ! $isAsync),
                fn (FormBuilder $form): FormBuilder => $form->precognitive()
            )
            ->when(
                $resource->isHideErrorsAtFormTop(),
                fn (FormBuilder $form): FormBuilder => $form->hideErrorsAtFormTop()
            )
            ->name($resource->uriKey())
            ->buttons($resource->getFormBuilderButtons())
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        $resource = $this->getResource();
        $item = $resource->getItem();

        $action = $resource->route(
            $item?->exists ? 'crud.update' : 'crud.store',
            $item?->getKey()
        );

        // Reset form problem
        $isAsync = $resource->isAsync();

        if (request()->boolean('_async_form')) {
            $isAsync = true;
        }

        return [
            Fragment::make([
                $this->formComponent(
                    $action,
                    $item,
                    $this->getResource()->getFormFields(),
                    $isAsync
                ),
            ])
                ->name('crud-form')
                ->updateAsync(['resourceItem' => $resource->getItemID()]),
        ];
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

            foreach ($outsideFields as $field) {
                $components[] = LineBreak::make();

                $components[] = Fragment::make([
                    $field->resolveFill(
                        $item?->attributesToArray() ?? [],
                        $item
                    ),
                ])->name($field->getRelationName());
            }
        }

        return array_merge($components, $this->getResource()->getFormPageComponents());
    }
}
