<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Core\TypeCasts\MixedDataCaster;

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

    public function unCastData(DataWrapperContract $data): array
    {
        return $data->toArray();
    }

    public function castData(mixed $data): DataWrapperContract
    {
        if ($data instanceof DataWrapperContract) {
            return $data;
        }

        if (! $this->hasCast()) {
            $this->cast(new MixedDataCaster());
        }

        return $this->getCast()->cast($data);
    }
}
