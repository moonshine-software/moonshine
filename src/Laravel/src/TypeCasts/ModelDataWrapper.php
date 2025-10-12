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
final readonly class ModelDataWrapper implements DataWrapperContract
{
    public function __construct(private Model $model)
    {
    }

    public function getOriginal(): Model
    {
        return $this->model;
    }

    public function getKey(): int|string|null
    {
        return $this->model->getKey();
    }

    public function toArray(): array
    {
        return $this->model->toArray();
    }

    public function __get(string $name): mixed
    {
        return $this->model->{$name};
    }

    public function __call(string $name, array $arguments)
    {
        return $this->model->{$name}($arguments);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->model->{$name} = $value;
    }

    public function __isset(string $name): bool
    {
        return $this->model->hasAttribute($name);
    }
}
