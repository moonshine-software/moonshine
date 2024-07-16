<?php

declare(strict_types=1);

namespace MoonShine\Core\TypeCasts;

use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;

final readonly class DefaultDataCaster implements DataCasterContract
{
    public function cast(mixed $data): CastedDataContract
    {
        return new DefaultCastedData($data);
    }

    public function paginatorCast(mixed $data): ?PaginatorContract
    {
        return null;
    }
}
