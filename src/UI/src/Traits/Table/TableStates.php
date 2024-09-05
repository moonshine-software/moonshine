<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Table;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\UI\Components\ActionButton;

trait TableStates
{
    protected bool $isPreview = false;

    protected bool $isVertical = false;

    protected bool $isEditable = false;

    protected bool $isCreatable = false;

    protected ?ActionButtonContract $creatableButton = null;

    protected bool $isReindex = false;

    protected bool $isPreparedReindex = false;

    protected bool $isReorderable = false;

    protected ?string $reorderableUrl = null;

    protected string $reorderableKey = 'id';

    protected ?string $reorderableGroup = null;

    protected bool $withNotFound = false;

    protected bool $isSimple = false;

    protected bool $isSticky = false;

    protected bool $isColumnSelection = false;

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
        ?ActionButtonContract $button = null,
    ): static {
        $this->isCreatable = true;
        $this->isReindex = $reindex;

        $this->creatableButton = $button
            ?: ActionButton::make($label ?? $this->getCore()->getTranslator()->get('moonshine::ui.add'), '#')
                ->icon($icon ?? 'plus-circle')
                ->customAttributes(
                    array_merge(['@click.prevent' => 'add()', 'class' => 'w-full'], $attributes)
                )
        ;

        if (! is_null($button)) {
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

    public function reorderable(
        ?string $url = null,
        string $key = 'id',
        ?string $group = null
    ): static {
        $this->isReorderable = true;
        $this->reorderableUrl = $url;
        $this->reorderableKey = $key;
        $this->reorderableGroup = $group;

        return $this;
    }

    public function isReorderable(): bool
    {
        return $this->isReorderable;
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

    public function searchable(): static
    {
        $this->searchable = true;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
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

    public function columnSelection(): static
    {
        $this->isColumnSelection = true;

        return $this;
    }

    public function isColumnSelection(): bool
    {
        return ! $this->isVertical() && $this->isColumnSelection;
    }

    public function clickAction(?ClickAction $action = null, ?string $selector = null): static
    {
        if (is_null($action)) {
            return $this;
        }

        return $this->customAttributes(array_filter([
            'data-click-action' => $action->value,
            'data-click-action-selector' => $selector,
        ]))->tdAttributes(
            static fn (): array => [
                '@click.stop' => 'rowClickAction',
            ]
        );
    }

    public function pushState(): static
    {
        return $this->customAttributes([
            'data-push-state' => 'true',
        ]);
    }

    public function removeAfterClone(): static
    {
        return $this->customAttributes([
            'data-remove-after-clone' => 1,
        ]);
    }

    /**
     * @return array{
     *     preview: bool,
     *     notfound: bool,
     *     creatable: bool,
     *     reindex: bool,
     *     reorderable: bool,
     *     simple: bool,
     *     sticky: bool,
     *     searchable: bool,
     *     searchValue: string,
     *     columnSelection: bool,
     * }
     */
    public function statesToArray(): array
    {
        return [
            'preview' => $this->isPreview(),
            'notfound' => $this->hasNotFound(),
            'creatable' => $this->isCreatable(),
            'reindex' => $this->isReindex(),
            'reorderable' => $this->isReorderable(),
            'simple' => $this->isSimple(),
            'sticky' => $this->isSticky(),
            'columnSelection' => $this->isColumnSelection(),
            'searchable' => $this->isSearchable(),
            'searchValue' => $this->getCore()->getRequest()->get('search', ''),
        ];
    }
}
