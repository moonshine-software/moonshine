<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\TypeCasts;

use MoonShine\Contracts\Core\Paginator\PaginatorContract;

/**
 * @template-covariant T
 */
interface DataCasterContract
{
    /**
     * @param T $data
     *
     * @return DataWrapperContract<T>
     */
    public function cast(mixed $data): DataWrapperContract;

    public function paginatorCast(mixed $data): ?PaginatorContract;
}
