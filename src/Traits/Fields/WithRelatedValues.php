<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Exceptions\FieldException;
use Throwable;

trait WithRelatedValues
{
    protected array $values = [];

    protected ?Collection $memoizeValues = null;

    protected ?Closure $valuesQuery = null;

    protected array $relatedColumns = [];

    protected function relatedColumns(array $relatedColumns): static
    {
        $this->relatedColumns = $relatedColumns;

        return $this;
    }

    protected function getMemoizeValues(): Collection
    {
        return $this->memoizeValues ?? collect();
    }

    public function valuesQuery(Closure $callback): static
    {
        $this->valuesQuery = $callback;

        return $this;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @throws Throwable
     */
    public function resolveValuesQuery(): Builder
    {
        $relation = $this->getRelation();

        throw_if(
            is_null($relation),
            new FieldException('Relation is required')
        );

        $related = $relation->getRelated();
        $query = $related->newModelQuery();

        if (is_closure($this->valuesQuery)) {
            $query = value($this->valuesQuery, $query, $this);
        }

        return $query;
    }

    protected function resolveRelatedQuery(Builder $builder): Collection
    {
        // #MongoDB Models fix
        $key = rescue(fn () => $builder->toRawSql(), fn (): bool => false, false);

        if($key === false) {
            return $builder->get();
        }

        return moonshineCache()->remember(
            sha1((string) $key),
            4,
            fn (): Collection => $builder->get()
        );
    }

    /**
     * @throws Throwable
     */
    public function values(): array
    {
        $query = $this->resolveValuesQuery();

        $formatted = ! is_null($this->formattedValueCallback());

        $values = $this->memoizeValues ?? $this->resolveRelatedQuery($query);
        $this->memoizeValues = $values;

        $getValue = fn (Model $item) => $formatted ? value(
            $this->formattedValueCallback(),
            $item,
            $this
        ) : data_get($item, $this->getResourceColumn());

        $values = $values->mapWithKeys(
            fn ($item): array => [
                $item->getKey() => $getValue($item),
            ]
        );

        $value = $this->toValue();

        if ($value instanceof Model && $value->exists && $values->isEmpty()) {
            $values->put(
                $value->getKey(),
                $getValue($value)
            );
        }

        return $values->toArray();
    }
}
