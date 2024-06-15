<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\MoonShineDataCaster;
use MoonShine\Core\TypeCasts\DefaultDataCast;

trait HasDataCast
{
    protected ?MoonShineDataCaster $cast = null;

    public function hasCast(): bool
    {
        return ! is_null($this->cast);
    }

    public function cast(MoonShineDataCaster $cast): static
    {
        $this->cast = $cast;

        return $this;
    }

    public function getCast(): MoonShineDataCaster
    {
        return $this->cast;
    }

    public function unCastData(CastedData $data): array
    {
        return $data->toArray();
    }

    public function castData(mixed $data): CastedData
    {
        if(! $this->hasCast()) {
            $this->cast(new DefaultDataCast());
        }

        return $this->getCast()->cast($data);
    }
}
