<?php

namespace MoonShine\Pages\Crud;

use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\DetailButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Pages\Page;

class FormPage extends Page
{
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

    public function components(): array
    {
        if (
            is_null($this->getResource()->getItem())
            && $this->getResource()->isNowOnUpdateForm()
        ) {
            oops404();
        }

        return array_merge(
            $this->topLayer(),
            $this->mainLayer(),
            $this->bottomLayer(),
        );
    }

    protected function topLayer(): array
    {
        $components = [];
        if (! empty($item = $this->getResource()->getItem())) {
            $components[] = Flex::make([
                ActionGroup::make([
                    ...$this->getResource()->getFormButtons(),
                    DetailButton::for($this->getResource()),
                    DeleteButton::for($this->getResource()),
                ])
                    ->setItem($item)
                ,
            ])
                ->customAttributes(['class' => 'mb-4'])
                ->justifyAlign('end');
        }

        return $components;
    }

    protected function mainLayer(): array
    {
        $item = $this->getResource()->getItem();

        $action = $this->getResource()->route(
            is_null($item) ? 'crud.store' : 'crud.update',
            $item?->getKey()
        );

        return [
            Fragment::make([
                form($action)
                    ->when(
                        moonshineRequest()->isFragmentLoad('crud-form'),
                        fn (FormBuilder $form): FormBuilder => $form->precognitive()
                    )
                    ->fields(
                        $this->getResource()
                            ->getFormFields()
                            ->when(
                                ! is_null($item),
                                fn (Fields $fields): Fields => $fields->push(
                                    Hidden::make('_method')->setValue('PUT')
                                )
                            )
                            ->toArray()
                    )
                    ->when(
                        $this->getResource()->isAsync(),
                        fn (FormBuilder $formBuilder): FormBuilder => $formBuilder->async()
                    )
                    ->when(
                        $this->getResource()->isPrecognitive(),
                        fn (FormBuilder $formBuilder): FormBuilder => $formBuilder->precognitive()
                    )
                    ->name('crud')
                    ->fill($item ? $this->getResource()->getModelCast()->dehydrate($item) : [])
                    ->cast($this->getResource()->getModelCast())
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']),
            ])->withName('crud-form'),
        ];
    }

    protected function bottomLayer(): array
    {
        $components = [];

        if (empty($item = $this->getResource()->getItem())) {
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
                ])->withName($field->getRelationName());
            }
        }

        return $components;
    }
}
