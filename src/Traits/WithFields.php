<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

use Leeto\MoonShine\Contracts\Fields\HasJsonValues;
use Leeto\MoonShine\Contracts\Fields\HasPivot;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Fields\Json;

trait WithFields
{
    protected array $fields = [];

    public function getFields(): Fields
    {
        $resolveChildFields = $this instanceof HasJsonValues
            || $this instanceof HasPivot
            || ($this instanceof HasResourceMode && !$this->isResourceMode());

        return Fields::make($this->fields)->when(
            $resolveChildFields,
            fn(Fields $fields) => $fields->resolveChildFields($this)
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
