<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait WithRelationship
{
    protected array $values = [];

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function values(): array
    {
        return $this->values;
    }

    public function relatedValues(Model $item): array
    {
        $related = $this->getRelated($item);

        if (is_callable($this->valueCallback())) {
            $values = $related->all()
                ->mapWithKeys(function ($relatedItem) {
                    return [$relatedItem->getKey() => ($this->valueCallback())($relatedItem)];
                });
        } else {
            $values = $related->pluck($this->resourceTitleField(), $related->getKeyName());
        }

        return $values->toArray();
    }

    public function isSelected(Model $item, string $value): bool
    {
        if (!$this->formViewValue($item)) {
            return false;
        }

        if (!$this->belongToOne() && !$this->toOne()) {
            $related = $this->getRelated($item);

            return $this->formViewValue($item) instanceof Collection
                ? $this->formViewValue($item)->contains($related->getKeyName(), '=', $value)
                : in_array($value, $this->formViewValue($item));
        }

        return (string)$this->formViewValue($item) === $value
            || (!$this->formViewValue($item) && (string)$this->getDefault() === $value);
    }
}
