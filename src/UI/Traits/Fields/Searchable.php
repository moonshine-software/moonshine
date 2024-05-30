<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

trait Searchable
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
