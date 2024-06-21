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
        $title = (new self)->getTitle($resource->getFilterParams());

        return ActionButton::make($title, '#')
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
            ]);
    }

    private function getTitle(array $params = []): string
    {
        $count = collect($params)
            ->filter(fn ($value): bool => $this->withoutEmptyFilter($value))
            ->count();

        return str(__('moonshine::ui.filters'))
            ->append('<span class="badge">' . ($count ?: '') . '</span>')
            ->value();
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
