<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\Paginator\PaginatorContract;

interface MoonShineDataCaster
{
    /**
     * @template-covariant T
     * @param T $data
     * @return CastedData<T>
     */
    public function cast(mixed $data): CastedData;

    public function paginatorCast(mixed $data): ?PaginatorContract;
}
