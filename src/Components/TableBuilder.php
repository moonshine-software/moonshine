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
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Fields\Fields;
use MoonShine\Table\TableRow;
use MoonShine\Traits\Makeable;

/**
 * @method static static make(array $fields = [], array $values = [], ?LengthAwarePaginator $paginator = null)
 TODO CrudMode, BlockMode, PreviewMode
 */
final class TableBuilder extends Component implements MoonShineRenderable, TableContract
{
    use Makeable;
    use Macroable;
    use Conditionable;

    protected $except = [
        'rows',
        'fields',
        'hasPaginator',
        'paginator',
    ];

    protected array $rows = [];

    protected array $buttons = [];

    protected ?string $typeCast = null;

    protected ?Closure $trAttributes = null;

    protected ?Closure $tdAttributes = null;

    public function __construct(
        protected array $fields = [],
        protected iterable $items = [],
        protected ?LengthAwarePaginator $paginator = null
    ) {
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
            # TODO[Cast]
            ->map(fn ($item) => $item->attributesToArray());
    }

    public function rows(): Collection
    {
        return $this->getItems()->map(function (array $data): TableRow {
            $castedValues = $this->castValues($data);

            return TableRow::make(
                $castedValues,
                $this->getFields()->fillClonedValues($data, $castedValues),
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

    public function cast(string $cast): self
    {
        $this->typeCast = $cast;

        return $this;
    }

    public function castValues(array $data): mixed
    {
        return $this->typeCast
            # TODO[Cast]
            ? (new $this->typeCast())->forceFill($data)
            : $data;
    }

    public function buttons(array $buttons = []): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(array $data): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->onlyVisible($this->castValues($data))
            ->fillItem($this->castValues($data))
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
        return view('moonshine::components.table.builder', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'rows' => $this->rows(),
            'fields' => $this->getFields(),
            'bulkButtons' => $this->getBulkButtons(),
            'hasPaginator' => $this->hasPaginator(),
            'paginator' => $this->getPaginator(),
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
