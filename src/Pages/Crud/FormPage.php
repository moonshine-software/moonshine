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
        $item = $this->getResource()->getItem();

        if (! is_null($item)) {
            $action = action(
                [CrudController::class, 'update'],
                [
                    'resourceUri' => $this->getResource()->uriKey(),
                    'resourceItem' => $item->getKey(),
                ]
            );
        } else {
            $action = action(
                [CrudController::class, 'store'],
                [
                    'resourceUri' => $this->getResource()->uriKey(),
                ]
            );
        }

        $components = [
            form($action)
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
                ->fill($item?->attributesToArray() ?? [])
                ->cast($this->getResource()->getModelCast()),
        ];

        foreach ($this->getResource()->getOutsideFields() as $field) {
            $components[] = $field->resolveFill(
                $item?->attributesToArray() ?? [],
                $item
            );
        }

        return $components;
    }
}
