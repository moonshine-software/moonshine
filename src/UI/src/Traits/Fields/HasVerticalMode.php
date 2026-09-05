<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;

trait HasVerticalMode
{
    protected bool $isVertical = false;

    protected int $verticalTitleSpan = 12;

    protected int $verticalValueSpan = 12;

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function vertical(Closure|bool|null $condition = null, int $titleSpan = 12, int $valueSpan = 12): static
    {
        $this->isVertical = value($condition, $this) ?? true;

        if ($this->isVertical()) {
            $this->verticalTitleSpan = $titleSpan;
            $this->verticalValueSpan = $valueSpan;
        }

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
    }
}
