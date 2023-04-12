<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use MoonShine\Fields\Fields;

trait WithFields
{
    protected array $fields = [];

    public function getFields(): Fields
    {
        $resolveChildFields = $this instanceof HasJsonValues
            || $this instanceof HasPivot
            || ($this instanceof HasResourceMode && ! $this->isResourceMode());

        if ($this instanceof HasFields && ! $this->manyToMany() && ! $this->hasFields()) {
            $this->fields(
                $this->resource()?->getFields()->withoutCanBeRelatable()?->toArray() ?? []
            );
        }

        return Fields::make($this->fields)->when(
            $resolveChildFields,
            fn (Fields $fields) => $fields->resolveChildFields($this)
        );
    }

    public function hasFields(): bool
    {
        return count($this->fields) > 0;
    }

    /**
     * @param  array  $fields
     * @return $this
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }
}
