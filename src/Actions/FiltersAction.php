<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Support\Arr;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;

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
        return $this->getResource()->currentRoute();
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
        return $this->filters === []
            ? $this->getResource()->getFilters()
            : Fields::make($this->filters)->wrapNames('filters');
    }

    public function getForm(): FormBuilder
    {
        return FormBuilder::make($this->url(), 'GET')
            ->fields(
                $this
                    ->getFilters()
                    ->when(
                        request('sort.column'),
                        static fn ($fields) => $fields
                            ->prepend(Hidden::make(column: 'sort.direction')->setValue(request('sort.direction')))
                            ->prepend(Hidden::make(column: 'sort.column')->setValue(request('sort.column')))
                    )
                    ->toArray()
            )
            ->fill(request('filters', []))
            ->submit(__('moonshine::ui.search'))
            ->when(
                request('filters'),
                static fn ($fields) => $fields->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $this->getResource()->currentRoute(query: ['reset' => true])
                    )->showInLine(),
                ])
            );
    }
}
