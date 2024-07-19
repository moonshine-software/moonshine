<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Core\TypeCasts\DefaultDataCaster;

trait HasDataCast
{
    protected ?DataCasterContract $cast = null;

    public function hasCast(): bool
    {
        return ! is_null($this->cast);
    }

    public function cast(DataCasterContract $cast): static
    {
        $this->cast = $cast;

        return $this;
    }

    public function getCast(): DataCasterContract
    {
        return $this->cast;
    }

    public function unCastData(CastedDataContract $data): array
    {
        return $data->toArray();
    }

    public function castData(mixed $data): CastedDataContract
    {
        if(! $this->hasCast()) {
            $this->cast(new DefaultDataCaster());
        }

        return $this->getCast()->cast($data);
    }
}
