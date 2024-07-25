<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\ActionButtonsContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Traits\HasDataCast;
use MoonShine\UI\Traits\WithFields;

abstract class IterableComponent extends MoonShineComponent implements HasFieldsContract
{
    use HasDataCast;
    use WithFields;

    protected iterable $items = [];

    protected ?PaginatorContract $paginator = null;

    protected iterable $buttons = [];

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
            $this->items = $items->getOriginalData();
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
        if(! is_null($this->paginator) && $async) {
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

    public function buttons(iterable $buttons = []): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function hasButtons(): bool
    {
        return $this->buttons !== [];
    }

    public function getButtons(CastedDataContract $data): ActionButtonsContract
    {
        return ActionButtons::make($this->buttons)
            ->fill($data)
            ->onlyVisible()
            ->withoutBulk();
    }

    public function getBulkButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->buttons)
            ->bulk($this->getName())
            ->onlyVisible();
    }
}
