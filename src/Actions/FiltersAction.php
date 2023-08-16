<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Support\Arr;
use MoonShine\Fields\Fields;

final class FiltersAction extends Action
{
    protected string $view = 'moonshine::actions.filters';

    protected ?string $icon = 'heroicons.outline.adjustments-horizontal';

    protected array $filters = [];

    protected bool $inDropdown = false;

    public function isTriggered(): bool
    {
        return false;
    }

    public function handle(): mixed
    {
        return null;
    }

    public function url(): string
    {
        return '';
    }

    public function filters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function activeCount(): int
    {
        return request()
            ->collect('filters')
            ->filter(
                fn ($filter) => is_array($filter) ? Arr::whereNotNull($filter)
                    : $filter
            )
            ->count();
    }

    public function getFilters(): Fields
    {
        $filters = !empty($this->filters)
            ? Fields::make($this->filters)->wrapNames('filters')
            : $this->getResource()->getFilters();

        $filters->fill(request('filters', []));

        return $filters;
    }
}
