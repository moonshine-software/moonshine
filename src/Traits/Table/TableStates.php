<?php

declare(strict_types=1);

namespace MoonShine\Traits\Table;

use MoonShine\ActionButtons\ActionButton;

trait TableStates
{
    protected bool $isPreview = false;

    protected bool $isVertical = false;

    protected bool $isEditable = false;

    protected bool $isCreatable = false;

    protected ?ActionButton $creatableButton = null;

    protected bool $isReindex = false;

    protected bool $isPreparedReindex = false;

    protected bool $isSortable = false;

    protected ?string $sortableUrl = null;

    protected string $sortableKey = 'id';

    protected ?string $sortableGroup = null;

    protected bool $withNotFound = false;

    protected bool $isSimple = false;

    protected bool $isSticky = false;

    protected bool $searchable = false;

    public function hasNotFound(): bool
    {
        return $this->withNotFound;
    }

    public function withNotFound(): static
    {
        $this->withNotFound = true;

        return $this;
    }

    public function preview(): static
    {
        $this->isPreview = true;

        return $this;
    }

    public function isPreview(): bool
    {
        return $this->isPreview;
    }

    public function editable(): static
    {
        $this->isEditable = true;

        return $this;
    }

    public function isEditable(): bool
    {
        return $this->isEditable;
    }

    public function vertical(): static
    {
        $this->isVertical = true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
    }

    public function creatable(
        bool $reindex = true,
        ?int $limit = null,
        ?string $label = null,
        ?string $icon = null,
        array $attributes = [],
        ?ActionButton $button = null,
    ): static {
        $this->isCreatable = true;
        $this->isReindex = $reindex;

        $this->creatableButton = $button
            ?: ActionButton::make($label ?? __('moonshine::ui.add'), '#')
                ->icon($icon ?? 'heroicons.plus-circle')
                ->customAttributes(
                    array_merge(['@click.prevent' => 'add()', 'class' => 'w-full'], $attributes)
                )
        ;

        if(! is_null($button)) {
            $button->customAttributes([
                '@click.prevent' => 'add()',
            ]);
        }

        if (! is_null($limit)) {
            $this->customAttributes([
                'data-creatable-limit' => $limit,
            ]);
        }

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function reindex(bool $prepared = false): static
    {
        $this->isReindex = true;
        $this->isPreparedReindex = $prepared;

        return $this;
    }

    public function isReindex(): bool
    {
        return $this->isReindex;
    }

    public function isPreparedReindex(): bool
    {
        return $this->isPreparedReindex;
    }

    public function sortable(
        ?string $url = null,
        string $key = 'id',
        ?string $group = null
    ): static {
        $this->isSortable = true;
        $this->sortableUrl = $url;
        $this->sortableKey = $key;
        $this->sortableGroup = $group;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }

    public function simple(): static
    {
        $this->isSimple = true;

        return $this;
    }

    public function isSimple(): bool
    {
        return $this->isSimple;
    }

    public function sticky(): static
    {
        $this->isSticky = true;

        return $this;
    }

    public function isSticky(): bool
    {
        return $this->isSticky;
    }

    public function searchable(): static
    {
        $this->searchable = true;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @return array{
     *     vertical: bool,
     *     editable: bool,
     *     preview: bool,
     *     notfound: bool,
     *     creatable: bool,
     *     reindex: bool,
     *     sortable: bool,
     *     simple: bool,
     *     sticky: bool,
     *     searchable: bool,
     *     searchValue: string,
     * }
     */
    public function statesToArray(): array
    {
        return [
            'vertical' => $this->isVertical(),
            'editable' => $this->isEditable(),
            'preview' => $this->isPreview(),
            'notfound' => $this->hasNotFound(),
            'creatable' => $this->isCreatable(),
            'reindex' => $this->isReindex(),
            'sortable' => $this->isSortable(),
            'simple' => $this->isSimple(),
            'sticky' => $this->isSticky(),
            'searchable' => $this->isSearchable(),
            'searchValue' => request()->input('search', ''),
        ];
    }
}
