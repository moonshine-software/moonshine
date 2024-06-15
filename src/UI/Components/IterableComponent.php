<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Collection;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Paginator\PaginatorContract;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Traits\HasDataCast;
use MoonShine\UI\Traits\WithFields;

abstract class IterableComponent extends MoonShineComponent implements HasFields
{
    use HasDataCast;
    use WithFields;

    protected iterable $items = [];

    protected ?PaginatorContract $paginator = null;

    protected array $buttons = [];

    public function items(iterable $items = []): static
    {
        $this->items = $items;

        return $this;
    }

    protected function resolvePaginator(): void
    {
        $items = $this->hasCast()
            ? $this->getCast()->paginatorCast($this->items)
            : $this->items;

        if ($items instanceof PaginatorContract) {
            $this->items = $items->getData();
            $this->paginator($items);
        }
    }

    public function getItems(): Collection
    {
        return collect($this->items)->filter();
    }

    public function paginator(PaginatorContract $paginator): static
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function getPaginator(bool $async = false): ?PaginatorContract
    {
        if(!is_null($this->paginator) && $async) {
            return $this->paginator->async();
        }

        return $this->paginator;
    }

    public function hasPaginator(): bool
    {
        return ! is_null($this->paginator);
    }

    public function isSimplePaginator(): bool
    {
        return $this->getPaginator()?->isSimple() ?? false;
    }

    public function buttons(array $buttons = []): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function hasButtons(): bool
    {
        return $this->buttons !== [];
    }

    public function getButtons(CastedData $data): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->fill($data)
            ->onlyVisible()
            ->withoutBulk();
    }

    public function getBulkButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->bulk($this->getName())
            ->onlyVisible();
    }
}
