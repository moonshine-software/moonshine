<?php

declare(strict_types=1);

namespace MoonShine\Forms;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Resources\ModelResource;
use Throwable;

final class FiltersForm
{
    /**
     * @throws Throwable
     */
    public function __invoke(ModelResource $resource): FormBuilder
    {
        $values = $resource->getFilterParams();
        $filters = $resource->getFilters();

        $action = $resource->isAsync() ? '#' : $resource->currentRoute();

        return FormBuilder::make($action, 'GET')
            ->fillCast($values, $resource->getModelCast())
            ->fields(
                $filters
                    ->when(
                        request('sort'),
                        static fn ($fields): Fields => $fields
                            ->prepend(Hidden::make(column: 'sort')->setValue(request('sort')))
                    )
                    ->when(
                        request('query-tag'),
                        static fn ($fields): Fields => $fields
                            ->prepend(Hidden::make(column: 'query-tag')->setValue(request('query-tag')))
                    )
                    ->toArray()
            )
            ->when($resource->isAsync(), function (FormBuilder $form) use ($resource): void {
                $form->customAttributes([
                    'x-on:submit.prevent' => 'asyncFilters(`'.$resource->listEventName().'`)',
                ]);

                $form->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $resource->currentRoute(query: ['reset' => true])
                    )
                        ->secondary()
                        ->showInLine()
                        ->customAttributes([
                            'id' => 'async-reset-button',
                            'style' => 'display: none',
                        ])
                    ,
                ]);
            })
            ->submit(__('moonshine::ui.search'), ['class' => 'btn-primary'])
            ->when(
                request('filters'),
                static fn ($fields): FormBuilder => $fields->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $resource->currentRoute(query: ['reset' => true])
                    )->secondary()->showInLine(),
                ])
            )
        ;
    }
}
