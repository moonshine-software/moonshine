<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait WithRelationship
{
    protected array $values = [];

    public function setValues(array $values): static
    {
        $this->values = $values;

        return $this;
    }

    public function values(): array
    {
        return $this->values;
    }

    public function resolveRelatedValues(Collection $values): array
    {
        if (is_callable($this->valueCallback())) {
            $values = $values->mapWithKeys(function (Model $relatedItem) {
                return [$relatedItem->getKey() => ($this->valueCallback())($relatedItem)];
            });
        } else {
            $values = $values->isEmpty()
                ? $values
                : $values->pluck($this->resourceTitleField(), $values->first()->getKeyName());
        }

        return $values->toArray();
    }
}
