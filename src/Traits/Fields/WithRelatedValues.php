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

    protected function relatedColumns(array $relatedColumns): self
    {
        $this->relatedColumns = $relatedColumns;

        return $this;
    }

    protected function getMemoizeValues(): Collection
    {
        return $this->memoizeValues ?? collect();
    }

    public function valuesQuery(Closure $callback): self
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
            $query = call_user_func($this->valuesQuery, $query);
        }

        return $query;
    }

    /**
     * @throws Throwable
     */
    public function values(): array
    {
        $query = $this->resolveValuesQuery();
        $related = $query->getModel();

        if (is_closure($this->formattedValueCallback())) {
            $values = $this->memoizeValues ?? $query->get();
            $this->memoizeValues = $values;

            $values = $values->mapWithKeys(
                fn ($item): array => [
                    $item->getKey() => call_user_func(
                        $this->formattedValueCallback(),
                        $item
                    ),
                ]
            );
        } else {
            $table = DB::getTablePrefix() . $related->getTable();
            $key = "$table.{$related->getKeyName()}";
            $column = "$table.{$this->getResourceColumn()}";

            $values = $this->memoizeValues
                ?? $query->selectRaw(
                    implode(',', [$key, $column, ...$this->relatedColumns])
                )->get();

            $this->memoizeValues = $values;

            $values = $values->pluck($this->getResourceColumn(), $related->getKeyName());
        }

        return $values->toArray();
    }
}
