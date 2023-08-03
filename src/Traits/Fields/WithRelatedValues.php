<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Support\Facades\DB;

trait WithRelatedValues
{
    protected array $values = [];

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

    public function values(): array
    {
        if (! empty($this->values)) {
            return $this->values;
        }

        if (is_null($this->getRelatedModel())) {
            return $this->values;
        }

        # TODO[refactor]
        $query = $this->getRelatedModel()->{$this->getRelation()}();
        $related = $query->getRelated();
        $query = $related->newModelQuery();

        if (is_callable($this->valuesQuery)) {
            $query = call_user_func($this->valuesQuery, $query);
        }

        if (is_callable($this->valueCallback())) {
            $values = $query->get()
                ->mapWithKeys(
                    fn ($item): array => [
                        $item->getKey() => ($this->valueCallback())(
                            $item
                        ),
                    ]
                );
        } else {
            $table = DB::getTablePrefix() . $related->getTable();
            $key = "$table.{$related->getKeyName()}";
            $column = "$table.{$this->getResource()->column()}";

            $values = $query->selectRaw("$key, $column")
                ->pluck($this->getResource()->column(), $related->getKeyName());
        }

        return $values->toArray();
    }
}
