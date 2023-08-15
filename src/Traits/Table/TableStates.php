<?php

declare(strict_types=1);

namespace MoonShine\Traits\Table;

trait TableStates
{
    protected bool $isAsync = false;

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

    public function withNotFound(): self
    {
        $this->withNotFound = true;

        return $this;
    }

    public function async(): self
    {
        $this->isAsync = true;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function preview(): self
    {
        $this->isPreview = true;

        return $this;
    }

    public function isPreview(): bool
    {
        return $this->isPreview;
    }

    public function editable(): self
    {
        $this->isEditable = true;

        return $this;
    }

    public function isEditable(): bool
    {
        return $this->isEditable;
    }

    public function vertical(): self
    {
        $this->isVertical = true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
    }

    public function creatable(bool $reindex = true): self
    {
        $this->isCreatable = true;
        $this->isReindex = $reindex;

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }


    public function isReindex(): bool
    {
        return $this->isReindex;
    }

    public function sortable(): self
    {
        $this->isSortable = true;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }

    public function simple(): self
    {
        $this->isSimple = true;

        return $this;
    }

    public function isSimple(): bool
    {
        return $this->isSimple;
    }

    public function statesToArray(): array
    {
        return [
            'async' => $this->isAsync(),
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
