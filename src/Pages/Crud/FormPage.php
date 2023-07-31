<?php

namespace MoonShine\Pages\Crud;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Hidden;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Pages\Page;

class FormPage extends Page
{
    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();

        if(request('item')) {
            $breadcrumbs[$this->route() . '/1'] = $this->getResource()->getItem()?->getKey();
        }

        return $breadcrumbs;
    }

    public function components(): array
    {
        $action = action(
            [CrudController::class, 'update'],
            [
                'resourceUri' => $this->getResource()->uriKey(),
                'item' => $this->getResource()->getItem()?->getKey(),
            ]
        );

        return [
            FormBuilder::make($action, 'POST')
                ->fields(
                    $this->getResource()
                        ->getFields()
                        ->push(Hidden::make('_method')->setValue('PUT'))
                        ->toArray()
                )
                ->fill($this->getResource()->getItem()?->toArray() ?? [])
                ->cast($this->getResource()->getModel()::class)
                ->submit('Go')
                ->buttons([
                    ActionButton::make('Yo', url: fn ($data): string => '/' . $data->title),
                ]),
        ];
    }
}
