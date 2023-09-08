<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Forms\FiltersForm;
use MoonShine\Resources\ModelResource;

final class FiltersButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(self::title(), '#')
            ->customAttributes(['class' => 'btn btn-secondary'])
            ->icon('heroicons.outline.adjustments-horizontal')
            ->inOffCanvas(
                fn (): array|string|null => __('moonshine::ui.filters'),
                fn (): FormBuilder => (new FiltersForm)($resource)
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
            ->when($count, fn (Stringable $str): Stringable => $str->append("($count)"))
            ->value();
    }
}
