<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\HasCasterContract;
use MoonShine\Contracts\UI\WithoutExtractionContract;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Traits\HasDataCast;

/**
 * @template TCaster of DataCasterContract
 * @template TWrapper of DataWrapperContract
 *
 * @implements HasCasterContract<DataCasterContract, DataWrapperContract>
 */
abstract class IterableComponent extends MoonShineComponent implements HasCasterContract, WithoutExtractionContract
{
    use HasDataCast;

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
        if (! is_null($this->paginator) && $async) {
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

    public function getButtons(DataWrapperContract $data): ActionButtonsContract
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
