<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Forms;

use Illuminate\Support\Arr;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Contracts\Fields\RangeField;
use MoonShine\UI\Contracts\Forms\FormContract;
use MoonShine\UI\Fields\Hidden;
use Stringable;
use Throwable;

final readonly class FiltersForm implements FormContract
{
    public function __construct(private ModelResource $resource)
    {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): FormBuilder
    {
        $resource = $this->resource;

        $resource->setQueryParams(
            request()->only($resource->getQueryParamsKeys())
        );

        $values = $resource->getFilterParams();
        $filters = $resource->getFilters();

        $action = $resource->isAsync() ? '#' : $this->getFormAction();

        foreach ($filters->onlyFields() as $filter) {
            if($filter instanceof RangeField) {
                data_forget($values, $filter->getColumn());
            }
        }

        return FormBuilder::make($action, FormMethod::GET)
            ->name('filters')
            ->fillCast($values, $resource->getModelCast())
            ->fields(
                $filters
                    ->when(
                        request()->input('sort'),
                        static fn ($fields): Fields => $fields
                            ->prepend(
                                Hidden::make(column: 'sort')->setValue(
                                    request()->input('sort')
                                )
                            )
                    )
                    ->when(
                        request()->input('query-tag'),
                        static fn ($fields): Fields => $fields
                            ->prepend(
                                Hidden::make(column: 'query-tag')->setValue(
                                    request()->input('query-tag')
                                )
                            )
                    )
                    ->toArray()
            )
            ->when($resource->isAsync(), function (FormBuilder $form) use ($resource): void {
                $events = [
                    $resource->getListEventName(),
                    'show-reset:filters',
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
                        $this->getFormAction(query: ['reset' => true])
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
                request()->has('filters'),
                fn (FormBuilder $form): FormBuilder => $form->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $this->getFormAction(query: ['reset' => true])
                    )->secondary()->showInLine(),
                ])
            );
    }

    private function getFormAction(array $query = []): string
    {
        return str(request()->url())->when(
            $query,
            static fn (Stringable $str): Stringable => $str
                ->append('?')
                ->append(Arr::query($query))
        )->value();
    }
}
