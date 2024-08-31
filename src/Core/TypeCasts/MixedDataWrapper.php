<?php

declare(strict_types=1);

namespace MoonShine\Core\TypeCasts;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

final readonly class MixedDataWrapper implements DataWrapperContract
{
    public function __construct(private mixed $data, private readonly string|int|null $key = null)
    {
    }

    public function getOriginal(): mixed
    {
        return $this->data;
    }

    public function getKey(): int|string|null
    {
        return $this->key;
    }

    public function toArray(): array
    {
        if(is_object($this->data) && method_exists($this->data, 'toArray')) {
            return $this->data->toArray();
        }

        return (array) $this->data;
    }
}
