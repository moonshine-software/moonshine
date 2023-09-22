<?php

declare(strict_types=1);

namespace MoonShine\TypeCasts;

use MoonShine\Contracts\MoonShineDataCast;
use MoonShine\Traits\Makeable;

/**
 * @template T
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
     * @param  array  $data
     * @return T
     */
    public function hydrate(array $data): mixed
    {
        $value = (new $this->class())
            ->setRelations($data['_relations'] ?? [])
            ->forceFill($data);

        $value->exists = true;

        return $value;
    }

    public function dehydrate(mixed $data): array
    {
        return $data->attributesToArray() + [
            '_relations' => $data->getRelations(),
        ];
    }
}
