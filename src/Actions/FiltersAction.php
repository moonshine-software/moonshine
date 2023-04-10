<?php

declare(strict_types=1);

namespace MoonShine\Actions;

final class FiltersAction extends Action
{
    protected static string $view = 'moonshine::crud.shared.filters';

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

    public function render(): string
    {
        return view($this->getView(), [
            'action' => $this,
            'filters' => count($this->filters)
                ? $this->filters
                : $this->resource()->getFilters()->toArray(),
            'resource' => $this->resource(),
        ])->render();
    }
}
