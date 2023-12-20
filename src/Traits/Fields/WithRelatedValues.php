<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    private function resolveRelatedQuery(Builder $builder): Collection
    {
        if (! method_exists($builder, 'toRawSql')) {
            return $builder->get();
        }

        return moonshineCache()->remember(
            sha1($builder->toRawSql()),
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
        $related = $query->getModel();

        if (is_closure($this->formattedValueCallback())) {
            $values = $this->memoizeValues ?? $this->resolveRelatedQuery($query);
            $this->memoizeValues = $values;

            $values = $values->mapWithKeys(
                fn ($item): array => [
                    $item->getKey() => value(
                        $this->formattedValueCallback(),
                        $item,
                        $this
                    ),
                ]
            );
        } else {
            $table = DB::getTablePrefix() . $related->getTable();
            $key = "$table.{$related->getKeyName()}";
            $column = "$table.{$this->getResourceColumn()}";

            $values = $this->memoizeValues
                ?? $this->resolveRelatedQuery(
                    $query->selectRaw(
                        implode(',', [$key, $column, ...$this->relatedColumns])
                    )
                );

            $this->memoizeValues = $values;

            $values = $values->pluck($this->getResourceColumn(), $related->getKeyName());
        }

        return $values->toArray();
    }
}
