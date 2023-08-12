<?php

declare(strict_types=1);

namespace MoonShine\Traits\Table;

trait TableStates
{
    protected bool $isAsync = false;

    protected bool $isPreview = false;

    protected bool $isVertical = false;

    protected bool $isEditable = false;

    protected bool $withNotFound = false;

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
}
