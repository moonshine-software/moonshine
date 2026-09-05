<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Laravel\Exceptions\ModelRelationFieldException;
use MoonShine\Support\DTOs\Select\Options;
use MoonShine\Support\Stringify;
use Throwable;

trait WithRelatedValues
{
    /**
     * @var array<array-key, string>
     */
    protected array $values = [];

    /**
     * @var Collection<array-key, Model>|null
     */
    protected ?Collection $memoizeValues = null;

    /**
     * @var (Closure(\Illuminate\Database\Eloquent\Builder<Model>, static): (\Illuminate\Database\Eloquent\Builder<Model>|\Illuminate\Database\Eloquent\Relations\Relation<Model, Model, covariant mixed>))|null
     */
    protected ?Closure $valuesQuery = null;

    /**
     * @var list<string>
     */
    protected array $relatedColumns = [];

    /**
     * @param list<string> $relatedColumns
     */
    protected function relatedColumns(array $relatedColumns): static
    {
        $this->relatedColumns = $relatedColumns;

        return $this;
    }

    /**
     * @return Collection<array-key, Model>
     */
    protected function getMemoizeValues(): Collection
    {
        return $this->memoizeValues ?? new Collection();
    }

    /**
     * @param Closure(\Illuminate\Database\Eloquent\Builder<Model>, static): (\Illuminate\Database\Eloquent\Builder<Model>|\Illuminate\Database\Eloquent\Relations\Relation<Model, Model, covariant mixed>) $callback
     */
    public function valuesQuery(Closure $callback): static
    {
        $this->valuesQuery = $callback;

        return $this;
    }

    /**
     * @param array<array-key, string> $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @throws Throwable
     * @return \Illuminate\Database\Eloquent\Builder<Model>|\Illuminate\Database\Eloquent\Relations\Relation<Model, Model, covariant mixed>
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
            return value($this->valuesQuery, $query, $this);
        }

        return $query;
    }

    /**
     * @return string|array<array-key, int|string>
     */
    protected function getSelectedValue(): string|array
    {
        return Stringify::value($this->getValue());
    }

    /**
     * @throws Throwable
     */
    public function getValues(): Options
    {
        $formatted = ! \is_null($this->getFormattedValueCallback());

        /** @var Collection<array-key, Model> $values */
        $values = $this->memoizeValues ?? ($this->isAsyncSearch() && ! $this->isToOne() ? Collection::wrap($this->toValue()) : $this->resolveValuesQuery()->get());

        if ($values === null || $values instanceof Collection) {
            $this->memoizeValues = $values;
        }

        $getValue = fn (Model $item): string => Stringify::value($formatted ? value(
            $this->getFormattedValueCallback(),
            $item,
            $this->getRowIndex(),
            $this
        ) : data_get($item, $this->getResourceColumn()));

        $values = $values->mapWithKeys(
            static fn ($item): array => [
                Stringify::value($item->getKey()) => $getValue($item),
            ]
        );

        // If the values are empty, add the selected model.
        if ($values->isEmpty() && ($value = $this->toValue()) instanceof Model && $value->exists) {
            $values->put(
                Stringify::value($value->getKey()),
                $getValue($value)
            );
        }

        return new Options(
            $values->all(),
            $this->getSelectedValue(),
            $this->getValuesWithProperties(onlyCustom: true)->all(),
        );
    }
}
