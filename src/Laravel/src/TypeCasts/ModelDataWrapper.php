<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

/**
 * @template T of Model
 *
 * @implements DataWrapperContract<T>
 */
final class ModelDataWrapper implements DataWrapperContract
{
    /**
     * Every filled field reads the same row, so the serialized model is shared between them.
     *
     * @var array<string, mixed>|null
     */
    private ?array $array = null;

    /**
     * @param T $model
     */
    public function __construct(private readonly Model $model)
    {
    }

    public function getOriginal(): Model
    {
        return $this->model;
    }

    public function getKey(): int|string|null
    {
        /** @var int|string|null */
        return $this->model->getKey();
    }

    public function toArray(): array
    {
        /** @var array<string, mixed> */
        return $this->array ??= $this->model->toArray();
    }

    public function __get(string $name): mixed
    {
        return $this->model->{$name};
    }

    /**
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        $this->array = null;

        return $this->model->{$name}($arguments);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->model->{$name} = $value;
        $this->array = null;
    }

    public function __isset(string $name): bool
    {
        return $this->model->hasAttribute($name);
    }
}
