<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Forms\FiltersForm;
use MoonShine\Resources\ModelResource;

final class FiltersButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        $count = collect($resource->getFilterParams())
            ->filter(fn ($value): bool => (new self())->withoutEmptyFilter($value))
            ->count();

        return ActionButton::make(__('moonshine::ui.filters'), '#')
            ->secondary()
            ->icon('heroicons.outline.adjustments-horizontal')
            ->inOffCanvas(
                fn (): array|string|null => __('moonshine::ui.filters'),
                fn (): FormBuilder => (new FiltersForm())($resource),
                name: 'filters-off-canvas'
            )
            ->showInLine()
            ->customAttributes([
                'class' => 'btn-filter',
            ])
            ->when(
                $resource->isAsync() || $count,
                fn (ActionButton $action) => $action->badge($count)
            );
    }

    private function withoutEmptyFilter(mixed $value): bool
    {
        if (is_iterable($value) && filled($value)) {
            return collect($value)
                ->filter(fn ($v): bool => $this->withoutEmptyFilter($v))
                ->isNotEmpty();
        }

        return ! blank($value) && $value !== "0";
    }
}
