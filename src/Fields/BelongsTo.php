<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\Fields\HasRelatedValues;
use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Traits\Fields\Searchable;

class BelongsTo extends Field implements HasRelationship, HasRelatedValues
{
    use Searchable;

    protected static string $component = 'BelongsTo';

    public function value(): array
    {
        return [
            'value' => $this->value?->getKey(),
            'key' => $this->value?->getKeyName(),
            'foreign_key' => $this->value?->getForeignKey(),
        ];
    }

    public function relatedValues(): Collection
    {
        if (!$this->value instanceof Model) {
            return Collection::make([]);
        }

        $values = $this->value->all();

        if (is_callable($this->valueCallback())) {
            return $values->mapWithKeys(function (Model $value) {
                return [$value->getKey() => ($this->valueCallback())($value)];
            });
        }

        return $values->isNotEmpty()
            ? $values->pluck($this->resourceColumn(), $this->value->getKeyName())
            : $values;
    }
}
