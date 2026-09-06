<?php

declare(strict_types=1);

namespace MoonShine\UI\Concerns;

use MoonShine\Contracts\Core\Paginator\PaginatorContract;

/**
 * @template TData of mixed
 */
trait HasPaginator
{
    /** @var PaginatorContract<TData>|null */
    protected ?PaginatorContract $paginator = null;

    /** @param PaginatorContract<TData> $paginator  */
    public function paginator(PaginatorContract $paginator): static
    {
        $this->paginator = $paginator;

        return $this;
    }

    /** @return  PaginatorContract<TData>|null  */
    public function getPaginator(bool $async = false): ?PaginatorContract
    {
        if (! \is_null($this->paginator) && $async) {
            return $this->paginator->async();
        }

        return $this->paginator;
    }

    /** @phpstan-assert-if-true PaginatorContract<TData> $this->getPaginator() */
    public function hasPaginator(): bool
    {
        return ! \is_null($this->paginator);
    }

    public function isSimplePaginator(): bool
    {
        return $this->getPaginator()?->isSimple() ?? false;
    }

    protected function resolvePaginator(): void
    {
        //
    }
}
