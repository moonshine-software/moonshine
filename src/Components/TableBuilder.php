<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Table\TableRow;
use MoonShine\Traits\Table\TableStates;

/**
 * @method static static make(array $fields = [], array $items = [], ?LengthAwarePaginator $paginator = null)
 */
final class TableBuilder extends IterableComponent implements TableContract
{
    use TableStates;

    protected $except = [
        'rows',
        'fields',
        'hasPaginator',
        'paginator',
    ];

    protected array $rows = [];

    protected ?Closure $trAttributes = null;

    protected ?Closure $tdAttributes = null;

    public function __construct(
        array $fields = [],
        protected iterable $items = [],
        protected ?LengthAwarePaginator $paginator = null
    ) {
        $this->fields($fields);

        if ($items instanceof LengthAwarePaginator) {
            $this->paginator($items);
            $this->items($items->items());
        }

        $this->withAttributes([]);
    }

    public function customAttributes(array $attributes): static
    {
        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    public function getItems(): Collection
    {
        return collect($this->items)
            ->map(
                fn ($item): array => $this->hasCast()
                ? $this->getCast()->dehydrate($item)
                : (array) $item
            );
    }

    public function rows(): Collection
    {
        return $this->getItems()->map(function (array $data): TableRow {
            $casted = $this->castData($data);

            return TableRow::make(
                $casted,
                $this->getFields()->fillCloned($data, $casted),
                $this->getButtons($data),
                $this->trAttributes,
                $this->tdAttributes
            );
        });
    }

    public function paginator(LengthAwarePaginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function getPaginator(): ?LengthAwarePaginator
    {
        return $this->paginator;
    }

    public function hasPaginator(): bool
    {
        return ! is_null($this->paginator);
    }

    public function getBulkButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->bulk();
    }

    public function trAttributes(Closure $closure): self
    {
        $this->trAttributes = $closure;

        return $this;
    }

    public function tdAttributes(Closure $closure): self
    {
        $this->tdAttributes = $closure;

        return $this;
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.table.builder', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'rows' => $this->rows(),
            'fields' => $this->getFields(),
            'hasPaginator' => $this->hasPaginator(),
            'paginator' => $this->getPaginator(),
            'bulkButtons' => $this->getBulkButtons(),
            'async' => $this->isAsync(),
            'vertical' => $this->isVertical(),
            'editable' => $this->isEditable(),
            'preview' => $this->isPreview(),
            'notfound' => $this->hasNotFound(),
        ]);
    }
}
