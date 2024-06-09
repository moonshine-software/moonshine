<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

interface MoonShineDataCast
{
    /**
     * @template-covariant T
     * @param T $data
     * @return CastedData<T>
     */
    public function cast(mixed $data): CastedData;
}
