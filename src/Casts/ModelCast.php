<?php

declare(strict_types=1);

namespace MoonShine\Casts;

use MoonShine\Contracts\MoonShineDataCast;
use MoonShine\Traits\Makeable;

/**
 * @method static static make(string $class)
 */
final class ModelCast implements MoonShineDataCast
{
    use Makeable;

    public function __construct(
        protected string $class
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function hydrate(array $data): mixed
    {
        return (new $this->class())
            ->setRelations($data['_relations'] ?? [])
            ->forceFill($data);
    }

    public function dehydrate(mixed $data): array
    {
        return $data->attributesToArray() + [
            '_relations' => $data->getRelations(),
        ];
    }
}
