<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\HasButtonsContract;
use MoonShine\Contracts\UI\HasCasterContract;
use MoonShine\Contracts\UI\HasPaginatorContract;
use MoonShine\Contracts\UI\WithoutExtractionContract;
use MoonShine\UI\Concerns\HasButtons;
use MoonShine\UI\Concerns\HasPaginator;
use MoonShine\UI\Traits\HasDataCast;

/**
 * @template TData of mixed = mixed
 * @template TCaster of DataCasterContract<TData> = DataCasterContract
 * @template TWrapper of DataWrapperContract<TData> = DataWrapperContract
 *
 * @implements HasCasterContract<TCaster, TWrapper>
 * @implements HasPaginatorContract<TData>
 */
abstract class IterableComponent extends MoonShineComponent implements
    HasCasterContract,
    HasPaginatorContract,
    HasButtonsContract,
    WithoutExtractionContract
{
    /** @use HasDataCast<TData, TCaster, TWrapper> */
    use HasDataCast;
    /** @use HasPaginator<TData> */
    use HasPaginator;
    use HasButtons;

    /**
     * @var iterable<TData>
     */
    protected iterable $items = [];

    /**
     * @var iterable<TData>
     */
    protected iterable $originalItems = [];

    protected ?Closure $itemsResolver = null;

    protected bool $itemsResolved = false;

    /**
     * @param  Closure(iterable<TData> $items, static $ctx): iterable<TData>  $resolver
     */
    public function itemsResolver(Closure $resolver): static
    {
        $this->itemsResolver = $resolver;

        return $this;
    }

    /**
     * @param  iterable<TData>  $items
     *
     */
    public function items(iterable $items = []): static
    {
        $this->items = $items;
        $this->originalItems = $items;

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

    /**
     * @api
     * @return iterable<TData>
     */
    public function getOriginalItems(): iterable
    {
        return $this->items;
    }

    /**
     * @return Collection<array-key, TData>|LazyCollection<array-key, TData>
     */
    public function getItems(): LazyCollection|Collection
    {
        if ($this->itemsResolved) {
            return $this->items instanceof LazyCollection
                ? $this->items
                : new Collection($this->items);
        }

        if (! \is_null($this->itemsResolver)) {
            $this->items = \call_user_func($this->itemsResolver, $this->items, $this);
        }

        $this->itemsResolved = true;

        return $this->items = $this->items instanceof LazyCollection
            ? $this->items
            : (new Collection($this->items))->filter();
    }
}
