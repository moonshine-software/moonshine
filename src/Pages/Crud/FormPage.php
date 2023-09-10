<?php

namespace MoonShine\Pages\Crud;

use MoonShine\Buttons\HasOneField\HasManyCreateButton;
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
        $resource = $this->getResource();

        $item = $resource->getItem();

        if (is_null($item) && $resource->isNowOnUpdateForm()) {
            oops404();
        }

        $action = $this->getResource()->route(
            is_null($item) ? 'crud.store' : 'crud.update',
            $item?->getKey()
        );

        $components = [];

        if (! empty($item)) {
            $components[] = Flex::make([
                ActionGroup::make([
                    ...$resource->getFormButtons(),
                    DetailButton::for($resource),
                    DeleteButton::for($resource),
                ])
                    ->setItem($item)
                ,
            ])
                ->customAttributes(['class' => 'mb-4'])
                ->justifyAlign('end')
            ;
        }

        $components[] = Fragment::make([
            form($action)
                ->when(
                    moonshineRequest()->isFragmentLoad('crud-form'),
                    fn (FormBuilder $form): FormBuilder => $form->precognitive()
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
                        ->toArray()
                )
                ->name('crud')
                ->fill($item?->attributesToArray() ?? [])
                ->cast($resource->getModelCast())
                ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']),
        ])->withName('crud-form');

        if (empty($item)) {
            return $components;
        }

        foreach ($resource->getOutsideFields() as $field) {
            $components[] = Divider::make($field->label());

            if (! $field->toOne()) {
                $components[] = HasManyCreateButton::for($field, $item->getKey());
                $components[] = Divider::make();
            }

            $components[] = Fragment::make([
                $field->resolveFill(
                    $item?->attributesToArray() ?? [],
                    $item
                )->value(withOld: false),
            ])->withName($field->getRelationName());
        }

        return $components;
    }
}
