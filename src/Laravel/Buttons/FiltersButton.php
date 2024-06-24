<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use MoonShine\Laravel\Forms\FiltersForm;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\OffCanvas;

final class FiltersButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        $form = moonshineConfig()->getForm('filters', FiltersForm::class, resource: $resource);

        $count = collect($resource->getFilterParams())
            ->filter(fn ($value): bool => (new self())->withoutEmptyFilter($value))
            ->count();

        return ActionButton::make(__('moonshine::ui.filters'), '#')
            ->secondary()
            ->icon('adjustments-horizontal')
            ->inOffCanvas(
                static fn (): array|string|null => __('moonshine::ui.filters'),
                static fn (): FormBuilder => $form,
                name: 'filters-off-canvas',
                builder: static fn (OffCanvas $offCanvas): OffCanvas => $offCanvas->setComponents([$form])
            )
            ->showInLine()
            ->class('btn-filter')
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

        return ! blank($value) && $value !== '0';
    }
}
