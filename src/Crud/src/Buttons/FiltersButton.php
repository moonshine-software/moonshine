<?php

declare(strict_types=1);

namespace MoonShine\Crud\Buttons;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\OffCanvasContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\UI\Components\ActionButton;

final class FiltersButton
{
    /**
     * @param  CrudResource $resource
     */
    public static function for(
        string $label,
        FormBuilderContract $form,
        CrudResourceContract $resource
    ): ActionButtonContract {
        $count = Collection::make($resource->getFilterParams())
            ->filter(fn ($value): bool => (new self())->withoutEmptyFilter($value))
            ->count();

        return ActionButton::make($label)
            ->name('filters-button')
            ->secondary()
            ->icon('adjustments-horizontal')
            ->inOffCanvas(
                static fn (): string => $label,
                static fn (): FormBuilderContract => $form,
                name: 'filters-off-canvas',
                builder: fn (OffCanvasContract $offCanvas): OffCanvasContract => $resource->resolveFiltersOffCanvas($offCanvas),
                components: [$form],
            )
            ->showInLine()
            ->class('js-filter-button')
            ->when(
                $resource->isAsync() || $count,
                fn (ActionButtonContract $action): ActionButtonContract => $action->badge($count)
            );
    }

    private function withoutEmptyFilter(mixed $value): bool
    {
        if (is_iterable($value) && filled($value)) {
            /** @var iterable<array-key, mixed> $value */
            return Collection::make($value)
                ->filter(fn (mixed $v): bool => $this->withoutEmptyFilter($v))
                ->isNotEmpty();
        }

        return ! blank($value) && $value !== '0';
    }
}
