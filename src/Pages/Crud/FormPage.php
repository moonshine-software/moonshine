<?php

namespace MoonShine\Pages\Crud;

use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Pages\Page;

class FormPage extends Page
{
    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();

        if ($this->getResource()->getItemID()) {
            $breadcrumbs[$this->route()] = $this->getResource()
                ?->getItem()
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

        $action = action(
            [CrudController::class, is_null($item) ? 'store' : 'update'],
            is_null($item)
                ? ['resourceUri' => $resource->uriKey()]
                : ['resourceUri' => $resource->uriKey(), 'resourceItem' => $item->getKey()]
        );

        $components = [
            form($action)
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
                ->fill($item?->attributesToArray() ?? [])
                ->cast($resource->getModelCast())
                ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']),
        ];

        foreach ($resource->getOutsideFields() as $field) {
            $components[] = $field->resolveFill(
                $item?->attributesToArray() ?? [],
                $item
            );
        }

        return $components;
    }
}
