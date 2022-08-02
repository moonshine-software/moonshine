<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

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

    public function resolveRelatedValues(array $values): array
    {
        if (is_callable($this->valueCallback())) {
            $values = collect($values)
                ->mapWithKeys(function ($relatedItem) {
                    return [$relatedItem->getKey() => ($this->valueCallback())($relatedItem)];
                });
        } else {
            # TODO $related->getKeyName()
            $values = collect($values)->pluck($this->resourceTitleField(), $related->getKeyName());
        }

        return $values->toArray();
    }
}
