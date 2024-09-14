<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

interface HasCasterContract
{
    public function hasCast(): bool;

    public function cast(DataCasterContract $cast): static;

    public function getCast(): DataCasterContract;

    public function unCastData(DataWrapperContract $data): array;

    public function castData(mixed $data): DataWrapperContract;
}
