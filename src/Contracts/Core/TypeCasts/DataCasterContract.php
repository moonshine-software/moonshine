<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\TypeCasts;

use MoonShine\Contracts\Core\Paginator\PaginatorContract;

interface DataCasterContract
{
    /**
     * @template-covariant T
     * @param T $data
     * @return CastedDataContract<T>
     */
    public function cast(mixed $data): CastedDataContract;

    public function paginatorCast(mixed $data): ?PaginatorContract;
}
