<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Resources\ModelResource;

final class FiltersButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(self::title(), '#')
            ->customAttributes(['class' => 'btn btn-pink'])
            ->icon('heroicons.outline.adjustments-horizontal')
            ->inOffCanvas(
                fn (): array|string|null => __('moonshine::ui.filters'),
                fn (): FormBuilder => FormBuilder::make($resource->currentRoute(), 'GET')
                    ->fields(
                        $resource
                            ->getFilters()
                            ->when(
                                request('sort.column'),
                                static fn ($fields): Fields => $fields
                                    ->prepend(
                                        Hidden::make(column: 'sort.direction')->setValue(request('sort.direction'))
                                    )
                                    ->prepend(Hidden::make(column: 'sort.column')->setValue(request('sort.column')))
                            )
                            ->toArray()
                    )
                    ->fill(request('filters', []))
                    ->submit(__('moonshine::ui.search'))
                    ->when(
                        request('filters'),
                        static fn ($fields): FormBuilder => $fields->buttons([
                            ActionButton::make(
                                __('moonshine::ui.reset'),
                                $resource->currentRoute(query: ['reset' => true])
                            )->showInLine(),
                        ])
                    )
            )
            ->showInLine();
    }

    private static function title(): string
    {
        $count = request()
            ->collect('filters')
            ->filter(
                fn ($filter) => is_array($filter) ? Arr::whereNotNull($filter)
                    : $filter
            )
            ->count();

        return str(__('moonshine::ui.filters'))
            ->when($count, fn(Stringable $str): Stringable => $str->append("($count)"))
            ->value();
    }
}
