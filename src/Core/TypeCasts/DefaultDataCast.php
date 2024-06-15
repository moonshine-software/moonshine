<?php

declare(strict_types=1);

namespace MoonShine\Core\TypeCasts;

use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\MoonShineDataCaster;
use MoonShine\Core\Paginator\PaginatorContract;

final readonly class DefaultDataCast implements MoonShineDataCaster
{
    public function cast(mixed $data): CastedData
    {
        return new DefaultCastedData($data);
    }

    public function paginatorCast(mixed $data): ?PaginatorContract
    {
        return null;
    }
}
