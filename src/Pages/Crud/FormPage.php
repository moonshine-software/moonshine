<?php

namespace MoonShine\Pages\Crud;

use MoonShine\Casts\ModelCast;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Pages\Page;

class FormPage extends Page
{
    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();

        if(request('crudItem')) {
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
        if(request('crudItem')) {
            $action = action(
                [CrudController::class, 'update'],
                [
                    'resourceUri' => $this->getResource()->uriKey(),
                    'crudItem' => $this->getResource()->getItem()?->getKey(),
                ]
            );
        } else {
            $action = action(
                [CrudController::class, 'store'],
                [
                    'resourceUri' => $this->getResource()->uriKey()
                ]
            );
        }

        return [
            form($action)
                ->fields(
                    $this->getResource()
                        ->getFields()
                        ->when(
                            request('crudItem'),
                            fn(Fields $fields): Fields => $fields->push(Hidden::make('_method')->setValue('PUT'))
                        )
                        ->toArray()
                )
                ->fill($this->getResource()->getItem()?->attributesToArray() ?? [])
                ->cast(ModelCast::make($this->getResource()->getModel()::class)),
        ];
    }
}
