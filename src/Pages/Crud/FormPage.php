<?php

namespace MoonShine\Pages\Crud;

use MoonShine\Buttons\DeleteButton;
use MoonShine\Buttons\DetailButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use Throwable;

/**
 * @method ModelResource getResource()
 */
class FormPage extends Page
{
    protected ?PageType $pageType = PageType::FORM;

    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();

        if ($this->getResource()->getItemID()) {
            $breadcrumbs[$this->route()] = $this->getResource()->getItem()
                ?->{$this->getResource()->column()};
        } else {
            $breadcrumbs[$this->route()] = __('moonshine::ui.add');
        }

        return $breadcrumbs;
    }

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
    }

    /**
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

    protected function topLayer(): array
    {
        $components = [];
        $item = $this->getResource()->getItem();

        if ($item?->exists) {
            $components[] = Flex::make([
                ActionGroup::make([
                    ...$this->getResource()->getFormButtons(),
                    DetailButton::for($this->getResource()),
                    DeleteButton::for(
                        $this->getResource(),
                        redirectAfterDelete: $this->getResource()->redirectAfterDelete()
                    ),
                ])
                    ->setItem($item)
                ,
            ])
                ->customAttributes(['class' => 'mb-4'])
                ->justifyAlign('end');
        }

        return $components;
    }

    /**
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

        $isForceAsync = request('_async_form', false);
        $isAsync = $resource->isAsync() || $isForceAsync;

        return [
            Fragment::make([
                FormBuilder::make($action)
                    ->fillCast(
                        $item,
                        $resource->getModelCast()
                    )
                    ->fields(
                        $resource
                            ->getFormFields()
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
                            ->async(asyncEvents: 'table-updated-' . request('_tableName', 'default'))
                    )
                    ->when(
                        $resource->isPrecognitive() || (moonshineRequest()->isFragmentLoad('crud-form') && ! $isAsync),
                        fn (FormBuilder $form): FormBuilder => $form->precognitive()
                    )
                    ->name('crud')
                    ->updateAsync()
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']),
            ])->name('crud-form'),
        ];
    }

    /**
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        $components = [];
        $item = $this->getResource()->getItem();

        if (! $item?->exists) {
            return $components;
        }

        $outsideFields = $this->getResource()->getOutsideFields();

        if ($outsideFields->isNotEmpty()) {
            $components[] = Divider::make();

            foreach ($this->getResource()->getOutsideFields() as $field) {
                $components[] = Fragment::make([
                    $field->resolveFill(
                        $item?->attributesToArray() ?? [],
                        $item
                    ),
                ])->name($field->getRelationName());
            }
        }

        return $components;
    }
}
