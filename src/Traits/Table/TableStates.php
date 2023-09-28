<?php

declare(strict_types=1);

namespace MoonShine\Traits\Table;

trait TableStates
{
    protected bool $isPreview = false;

    protected bool $isVertical = false;

    protected bool $isEditable = false;

    protected bool $isCreatable = false;

    protected bool $isReindex = false;

    protected bool $isSortable = false;

    protected bool $withNotFound = false;

    protected bool $isSimple = false;

    public function hasNotFound(): bool
    {
        return $this->withNotFound;
    }

    public function withNotFound(): static
    {
        $this->withNotFound = true;

        return $this;
    }

    public function preview(): static
    {
        $this->isPreview = true;

        return $this;
    }

    public function isPreview(): bool
    {
        return $this->isPreview;
    }

    public function editable(): static
    {
        $this->isEditable = true;

        return $this;
    }

    public function isEditable(): bool
    {
        return $this->isEditable;
    }

    public function vertical(): static
    {
        $this->isVertical = true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
    }

    public function creatable(bool $reindex = true): static
    {
        $this->isCreatable = true;
        $this->isReindex = $reindex;

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function reindex(): static
    {
        $this->isReindex = true;

        return $this;
    }

    public function isReindex(): bool
    {
        return $this->isReindex;
    }

    public function sortable(): static
    {
        $this->isSortable = true;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }

    public function simple(): static
    {
        $this->isSimple = true;

        return $this;
    }

    public function isSimple(): bool
    {
        return $this->isSimple;
    }

    /**
     * @return array{async: mixed, vertical: mixed, editable: mixed, preview: mixed, notfound: mixed, creatable: mixed, reindex: mixed, sortable: mixed, simple: mixed}
     */
    public function statesToArray(): array
    {
        return [
            'vertical' => $this->isVertical(),
            'editable' => $this->isEditable(),
            'preview' => $this->isPreview(),
            'notfound' => $this->hasNotFound(),
            'creatable' => $this->isCreatable(),
            'reindex' => $this->isReindex(),
            'sortable' => $this->isSortable(),
            'simple' => $this->isSimple(),
        ];
    }
}
