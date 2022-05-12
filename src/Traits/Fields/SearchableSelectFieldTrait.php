<?php

namespace Leeto\MoonShine\Traits\Fields;

trait SearchableSelectFieldTrait
{
    protected bool $searchable = false;

    protected string|null $searchableImageField = null;

    public function searchable(string|null $searchableImageField = null): static
    {
        $this->searchable = true;
        $this->searchableImageField = $searchableImageField;

        return $this;
    }

    public function searchableImageField(): string|null
    {
        return $this->searchableImageField;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}