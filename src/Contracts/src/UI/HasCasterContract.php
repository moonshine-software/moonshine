<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

/**
 * @template TCaster of DataCasterContract
 * @template TWrapper of DataWrapperContract
 */
interface HasCasterContract
{
    public function hasCast(): bool;

    /**
     * @param  TCaster  $cast
     */
    public function cast(DataCasterContract $cast): static;

    /**
     * @return TCaster
     */
    public function getCast(): DataCasterContract;

    /**
     * @param  TWrapper  $data
     */
    public function unCastData(DataWrapperContract $data): array;

    /**
     * @return TWrapper
     */
    public function castData(mixed $data): DataWrapperContract;
}
