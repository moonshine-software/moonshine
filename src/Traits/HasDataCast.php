<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Contracts\MoonShineDataCast;

trait HasDataCast
{
    protected ?MoonShineDataCast $cast = null;

    public function hasCast(): bool
    {
        return ! is_null($this->cast);
    }

    public function cast(MoonShineDataCast $cast): self
    {
        $this->cast = $cast;

        return $this;
    }

    public function getCast(): MoonShineDataCast
    {
        return $this->cast;
    }

    public function castData(array $data): mixed
    {
        return $this->hasCast()
            ? $this->getCast()->hydrate($data)
            : $data;
    }
}
