<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Laravel\Exceptions\ModelRelationFieldException;
use MoonShine\Support\DTOs\Select\Options;
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
        return $this->memoizeValues ?? new Collection();
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

        if (\is_null($relation)) {
            throw ModelRelationFieldException::relationRequired();
        }

        $related = $relation->getRelated();
        $query = $related->newQuery();

        if (! \is_null($this->valuesQuery)) {
            $query = value($this->valuesQuery, $query, $this);
        }

        return $query;
    }

    protected function getSelectedValue(): string|array
    {
        return (string) $this->getValue();
    }

    /**
     * @throws Throwable
     */
    public function getValues(): Options
    {
        $formatted = ! \is_null($this->getFormattedValueCallback());

        $values = $this->memoizeValues ?? ($this->isAsyncSearch() && ! $this->isToOne() ? $this->toValue() : $this->resolveValuesQuery()->get());

        if ($values === null || $values instanceof Collection) {
            $this->memoizeValues = $values;
        }

        $getValue = fn (Model $item) => $formatted ? value(
            $this->getFormattedValueCallback(),
            $item,
            $this
        ) : data_get($item, $this->getResourceColumn());

        $values = $values->mapWithKeys(
            static fn ($item): array => [
                $item->getKey() => $getValue($item),
            ]
        );

        $toOptions = fn (array $values): Options => new Options(
            $values,
            $this->getSelectedValue(),
            $this->getValuesWithProperties(onlyCustom: true)->toArray()
        );

        if ($values->isNotEmpty()) {
            return $toOptions(
                $values->toArray()
            );
        }

        $value = $this->toValue();

        // if the values are empty then we add the selected one
        if ($value instanceof Model && $value->exists && $values->isEmpty()) {
            $values->put(
                $value->getKey(),
                $getValue($value)
            );
        }

        return $toOptions(
            $values->toArray(),
        );
    }
}
