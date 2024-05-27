<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use MoonShine\Core\Contracts\MoonShineDataCast;
use MoonShine\Support\Traits\Makeable;

/**
 * @template-covariant T of Model
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
     * @return Model
     */
    public function hydrate(array $data): mixed
    {
        /** @var Model $value */
        $value = (new ($this->getClass())());

        $value
            ->forceFill([
                $value->getKeyName() => $data[$value->getKeyName()] ?? null,
                ...collect($data)->filter(fn ($item): bool => is_scalar($item))->toArray(),
            ])
            ->fill(
                collect($data)
                    ->except([$value->getKeyName(), $value->getUpdatedAtColumn(), $value->getCreatedAtColumn()])
                    ->toArray()
            )
            ->setRelations($data['_relations'] ?? []);

        $value->exists = ! empty($value->getKey());

        return $value;
    }

    /**
     * @param  Model  $data
     */
    public function dehydrate(mixed $data): array
    {
        if(! $data instanceof Model) {
            throw new InvalidArgumentException('Model is required');
        }

        return $data->attributesToArray() + [
            '_relations' => $data->getRelations(),
        ];
    }
}
