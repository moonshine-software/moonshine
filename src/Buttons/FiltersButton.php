<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\Offcanvas;
use MoonShine\Forms\FiltersForm;
use MoonShine\Resources\ModelResource;

final class FiltersButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        $title = self::title($resource->getFilterParams());

        $filters = call_user_func(new FiltersForm, $resource);

        return ActionButton::make($title, '#')
            ->secondary()
            ->icon('heroicons.outline.adjustments-horizontal')
            ->inOffCanvas(
                fn (): array|string|null => __('moonshine::ui.filters'),
                fn (): FormBuilder => $filters,
                name: 'filters-off-canvas',
                builder: fn(Offcanvas $offCanvas): Offcanvas => $offCanvas->setComponents([$filters])
            )
            ->showInLine();
    }

    private static function title(array $params = []): string
    {
        $count = collect($params)
            ->filter(
                fn ($filter) => is_array($filter) ? Arr::whereNotNull($filter)
                    : filled($filter)
            )
            ->count();

        return str(__('moonshine::ui.filters'))
            ->when($count, fn (Stringable $str): Stringable => $str->append("($count)"))
            ->value();
    }
}
