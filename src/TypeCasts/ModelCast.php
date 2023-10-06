<?php

declare(strict_types=1);

namespace MoonShine\TypeCasts;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\MoonShineDataCast;
use MoonShine\Traits\Makeable;

/**
 * @template T of Model
 * @method static static make(string $class)
 */
final class ModelCast implements MoonShineDataCast
{
    use Makeable;

    /**
     * @param  class-string<T>  $class
     */
    public function __construct(
        protected string $class
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return T
     */
    public function hydrate(array $data): mixed
    {
        /** @var T $value */
        $value = (new ($this->getClass())());

        $value
            ->forceFill([
                $value->getKeyName() => $data[$value->getKeyName()] ?? null
            ])
            ->fill($data)
            ->setRelations($data['_relations'] ?? []);

        $value->exists = ! empty($value->getKey());

        return $value;
    }

    /**
     * @param  T  $data
     */
    public function dehydrate(mixed $data): array
    {
        return $data->attributesToArray() + [
                '_relations' => $data->getRelations(),
            ];
    }
}
