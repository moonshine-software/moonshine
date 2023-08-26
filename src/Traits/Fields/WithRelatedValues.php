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
        if (! is_null($this->memoizeValues)) {
            return $this->memoizeValues->toArray();
        }

        $query = $this->resolveValuesQuery();
        $related = $query->getModel();

        if (is_closure($this->formattedValueCallback())) {
            $values = $query->get()
                ->mapWithKeys(
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

            $values = $query->selectRaw("$key, $column")
                ->pluck($this->getResourceColumn(), $related->getKeyName());
        }

        $this->memoizeValues = $values;

        return $this->memoizeValues->toArray();
    }
}
