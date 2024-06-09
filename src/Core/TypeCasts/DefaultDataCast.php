<?php

declare(strict_types=1);

namespace MoonShine\Core\TypeCasts;

use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\MoonShineDataCast;

final readonly class DefaultDataCast implements MoonShineDataCast
{
    public function cast(mixed $data): CastedData
    {
        return new DefaultCastedData($data);
    }
}
