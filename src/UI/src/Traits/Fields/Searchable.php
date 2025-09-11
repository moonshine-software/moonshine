<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;

trait Searchable
{
    protected bool $searchable = false;

    public function searchable(Closure|bool $condition = true): static
    {
        $this->searchable = value($condition, $this);

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}
