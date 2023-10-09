<?php

declare(strict_types=1);

namespace MoonShine\Forms;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Resources\ModelResource;

final class FiltersForm
{
    public function __invoke(ModelResource $resource): FormBuilder
    {
        $values = $resource->getFilterParams();
        $filters = $resource->getFilters();
        $filters->fill($values, $resource->getModel());

        return FormBuilder::make($resource->currentRoute(), 'GET')
            ->name('filters-form')
            ->cast($resource->getModelCast())
            ->fields(
                $filters
                    ->when(
                        request('sort.column'),
                        static fn ($fields): Fields => $fields
                            ->prepend(Hidden::make(column: 'sort.direction')->setValue(request('sort.direction')))
                            ->prepend(Hidden::make(column: 'sort.column')->setValue(request('sort.column')))
                    )
                    ->when(
                        request('query-tag'),
                        static fn ($fields): Fields => $fields
                            ->prepend(Hidden::make(column: 'query-tag')->setValue(request('query-tag')))
                    )
                    ->toArray()
            )
            ->fill($values)
            ->submit(__('moonshine::ui.search'))
            ->when(
                request('filters'),
                static fn ($fields): FormBuilder => $fields->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $resource->currentRoute(query: ['reset' => true])
                    )->showInLine(),
                ])
            );
    }
}
