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

    public function cast(MoonShineDataCast $cast): static
    {
        $this->cast = $cast;

        return $this;
    }

    public function getCast(): MoonShineDataCast
    {
        return $this->cast;
    }

    public function unCastData(mixed $data): mixed
    {
        if($this->hasCast()) {
            $class = $this->getCast()->getClass();

            return $data instanceof $class
                ? $this->getCast()->dehydrate($data)
                : $data;
        }

        return $data;
    }

    public function castData(mixed $data): mixed
    {
        if($this->hasCast()) {
            $class = $this->getCast()->getClass();

            return !$data instanceof $class
                ? $this->getCast()->hydrate($data)
                : $data;
        }

        return $data;
    }
}
