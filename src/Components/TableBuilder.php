<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Fields\Fields;
use MoonShine\Table\TableRow;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\Table\TableStates;

/**
 * @method static make(array $fields = [], array $values = [], ?LengthAwarePaginator $paginator = null)
 */
final class TableBuilder extends Component implements MoonShineRenderable, TableContract
{
    use Makeable;
    use Macroable;
    use TableStates;
    use HasDataCast;
    use Conditionable;

    protected $except = [
        'rows',
        'fields',
        'hasPaginator',
        'paginator',
    ];

    protected array $rows = [];

    protected array $buttons = [];

    protected ?Closure $trAttributes = null;

    protected ?Closure $tdAttributes = null;

    public function __construct(
        protected array $fields = [],
        protected iterable $items = [],
        protected ?LengthAwarePaginator $paginator = null
    ) {
        $this->withAttributes([]);
    }

    public function customAttributes(array $attributes): static
    {
        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getFields(): Fields
    {
        return Fields::make($this->fields);
    }

    public function items(iterable $items = []): self
    {
        $this->items = $items;

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

    public function buttons(array $buttons = []): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(array $data): ActionButtons
    {
        $casted = $this->castData($data);

        return ActionButtons::make($this->buttons)
            ->onlyVisible($casted)
            ->fillItem($casted)
            ->withoutBulk();
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
        $this->customAttributes([
            'x-data' => "tableBuilder({$this->isAsync()})",
        ]);

        if($this->isRemovable()) {
            $this->buttons([
                ActionButton::make(
                    '',
                    '#'
                )
                    ->onClick(fn (): string => 'remove()', 'prevent')
                    ->icon('heroicons.outline.trash')
                    ->showInLine(),
            ]);
        }

        return view('moonshine::components.table.builder', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'rows' => $this->rows(),
            'fields' => $this->getFields(),
            'hasPaginator' => $this->hasPaginator(),
            'paginator' => $this->getPaginator(),
            'bulkButtons' => $this->getBulkButtons(),
            'vertical' => $this->isVertical(),
            'preview' => $this->isPreview(),
            'creatable' => $this->isCreatable(),
            'editable' => $this->isEditable(),
            'removable' => $this->isRemovable(),
            'notfound' => $this->hasNotFound(),
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
