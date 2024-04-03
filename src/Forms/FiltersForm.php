<?php

declare(strict_types=1);

namespace MoonShine\Forms;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
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
            ->name('filters')
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
                $events = [
                    $resource->listEventName(),
                    'disable-query-tags',
                    'show-reset-filters',
                    AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'filters-off-canvas'),
                ];

                $form->customAttributes([
                    '@submit.prevent' => "asyncFilters(
                        `" . AlpineJs::prepareEvents($events) . "`,
                        `_component_name,_token,_method`
                    )",
                ]);

                $form->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $resource->currentRoute(query: ['reset' => true])
                    )
                        ->secondary()
                        ->showInLine()
                        ->customAttributes([
                            AlpineJs::eventBlade('show-reset', 'filters') => "showResetButton",
                            'style' => 'display: none',
                            'id' => 'async-reset-button',
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
