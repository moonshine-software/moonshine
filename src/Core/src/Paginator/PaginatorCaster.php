<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use MoonShine\Contracts\Core\Paginator\PaginatorCasterContract;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;

final readonly class PaginatorCaster implements PaginatorCasterContract
{
    /**
     * @param array{
     *     path: string,
     *     links: iterable<string, mixed>,
     *     data: iterable<array-key, mixed>,
     *     originalData: iterable<array-key, mixed>,
     *     currentPage: int, from: int|null, to: int|null, perPage: int,
     *     simple?: bool, total?: int|null, lastPage?: int|null,
     *     firstPageUrl?: string|null, prevPageUrl?: string|null,
     *     lastPageUrl?: string|null, nextPageUrl?: string|null,
     *     pageName?: string, translates?: array<string, string>
     * }|array{
     *     string, iterable<string, mixed>, iterable<array-key, mixed>, iterable<array-key, mixed>,
     *     int, int|null, int|null, int, 8?: bool, 9?: int|null, 10?: int|null,
     *     11?: string|null, 12?: string|null, 13?: string|null, 14?: string|null, 15?: string, 16?: array<string, string>
     * } $data
     */
    public function __construct(private array $data)
    {
    }

    public function cast(): PaginatorContract
    {
        return new Paginator(...$this->data);
    }
}
