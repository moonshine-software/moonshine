<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait WithRelatedValues
{
    protected array $values = [];

    protected ?Closure $valuesQuery = null;

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function values(): array
    {
        return $this->values;
    }

    public function valuesQuery(Closure $callback): self
    {
        $this->valuesQuery = $callback;

        return $this;
    }

    public function relatedValues(Model $item): array
    {
        $related = $this->getRelated($item);
        $query = $related->newModelQuery();

        if (is_callable($this->valuesQuery)) {
            $query = call_user_func($this->valuesQuery, $query);
        }

        if (is_callable($this->valueCallback())) {
            $values = $query->get()
                ->mapWithKeys(function ($relatedItem) {
                    return [$relatedItem->getKey() => ($this->valueCallback())($relatedItem)];
                });
        } else {
            $tableName = DB::getTablePrefix().$related->getTable();
            $values = $query->selectRaw("{$tableName}.{$related->getKeyName()}, {$tableName}.{$this->resourceTitleField()}")
                ->pluck($this->resourceTitleField(), $related->getKeyName());
        }

        return $values->toArray();
    }
}
