<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\Laravel\Forms\FiltersForm;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\OffCanvas;

final class FiltersButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        $title = self::getTitle($resource->getFilterParams());

        $form = moonshineConfig()->getForm('filters', FiltersForm::class, resource: $resource);

        return ActionButton::make($title, '#')
            ->secondary()
            ->icon('adjustments-horizontal')
            ->inOffCanvas(
                fn (): array|string|null => __('moonshine::ui.filters'),
                fn (): FormBuilder => $form,
                name: 'filters-off-canvas',
                builder: fn (OffCanvas $offCanvas): OffCanvas => $offCanvas->setComponents([$form])
            )
            ->showInLine();
    }

    private static function getTitle(array $params = []): string
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
