<?php

namespace Leeto\MoonShine\Traits\Fields;

trait SearchableTrait
{
    protected bool $searchable = false;

    public function searchable(): static
    {
        $this->searchable = true;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}