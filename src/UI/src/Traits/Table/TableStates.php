<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Table;

use Closure;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\UI\Components\ActionButton;

trait TableStates
{
    protected bool $isPreview = false;

    protected bool $isVertical = false;

    protected null|Closure|int $verticalTitleCallback = null;

    protected null|Closure|int $verticalValueCallback = null;

    protected bool $isEditable = false;

    protected bool $isCreatable = false;

    protected ?ActionButtonContract $creatableButton = null;

    protected bool $isReindex = false;

    protected bool $isPreparedReindex = false;

    protected bool $isReorderable = false;

    protected ?string $reorderableUrl = null;

    protected ?string $reorderableKey = null;

    protected ?string $reorderableGroup = null;

    protected bool $withNotFound = false;

    protected bool $isSimple = false;

    protected bool $isSticky = false;

    protected bool $isStickyButtons = false;

    protected bool $isLazy = false;

    protected bool $isColumnSelection = false;

    protected bool $searchable = false;

    protected bool $withLoader = false;

    protected bool $withSkeleton = true;

    protected string $queryParamPrefix = '';

    public function queryParamPrefix(string $prefix): static
    {
        $this->queryParamPrefix = trim($prefix);

        $this->customAttributes([
            'data-query-param-prefix' => $this->queryParamPrefix,
        ]);

        return $this;
    }

    public function withFilters(string $formName): static
    {
        return $this->customAttributes([
            'data-filters-form-name' => $formName,
        ]);
    }

    public function hasLoader(): bool
    {
        return $this->withLoader;
    }

    public function loader(Closure|bool|null $condition = null): static
    {
        $this->withLoader = value($condition, $this) ?? true;

        return $this;
    }

    public function hasSkeleton(): bool
    {
        return $this->withSkeleton;
    }

    public function skeleton(Closure|bool|null $condition = null): static
    {
        $this->withSkeleton = value($condition, $this) ?? true;

        return $this;
    }

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

    /**
     * @param  null|int|Closure(FieldContract $field, ComponentContract $default, static $ctx): ComponentContract  $title
     * @param  null|int|Closure(FieldContract $field, ComponentContract $default, static $ctx): ComponentContract  $value
     */
    public function vertical(null|Closure|int $title = null, null|Closure|int $value = null): static
    {
        $this->isVertical = true;
        $this->verticalTitleCallback = $title;
        $this->verticalValueCallback = $value;

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
            ?: ActionButton::make($label ?? $this->getCore()->getTranslator()->get('moonshine::ui.add'))
                ->icon($icon ?? 'plus-circle')
                ->customAttributes(
                    array_merge(['@click.prevent' => 'add()', 'class' => 'w-full'], $attributes),
                )
        ;

        if (! \is_null($button)) {
            $button->customAttributes([
                '@click.prevent' => 'add()',
            ]);
        }

        if (! \is_null($limit)) {
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
        ?string $key = null,
        ?string $group = null,
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

    public function stickyButtons(): static
    {
        $this->isStickyButtons = true;

        return $this;
    }

    public function isStickyButtons(): bool
    {
        return $this->isStickyButtons;
    }

    public function getStickyClass(): string
    {
        return 'sticky-col';
    }

    public function lazy(): static
    {
        $this->isLazy = true;

        return $this;
    }

    public function isLazy(): bool
    {
        return $this->isLazy;
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
        if (\is_null($action)) {
            return $this;
        }

        return $this->customAttributes(array_filter([
            'data-click-action' => $action->value,
            'data-click-action-selector' => $selector,
        ]))->tdAttributes(
            static fn (): array => [
                '@click' => 'rowClickAction',
            ],
        );
    }

    public function pushState(): static
    {
        return $this->customAttributes([
            'data-push-state' => 'true',
        ]);
    }

    /**
     * Remove empty table row after clone
     */
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
     *     stickyButtons: bool,
     *     searchable: bool,
     *     searchValue: string,
     *     columnSelection: bool,
     *     skeleton: bool,
     *     loader: bool,
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
            'stickyButtons' => $this->isStickyButtons(),
            'lazy' => $this->isLazy(),
            'columnSelection' => $this->isColumnSelection(),
            'searchable' => $this->isSearchable(),
            'searchValue' => $this->getCore()->getRequest()->getScalar('search', ''),
            'skeleton' => true,
            'loader' => true,
        ];
    }
}
