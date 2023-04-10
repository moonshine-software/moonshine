<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use InvalidArgumentException;

trait WithColumnSpan
{
    protected int $columnSpan = 12;

    protected int $adaptiveColumnSpan = 12;

    public function columnSpan(int $columnSpan, int $adaptiveColumnSpan = 12): static
    {
        if (($columnSpan <= 0 || $columnSpan > 12) && ($adaptiveColumnSpan <= 0 || $adaptiveColumnSpan > 12)) {
            throw new InvalidArgumentException(
                'columnSpan must be greater than zero and less than 12'
            );
        }

        $this->columnSpan = $columnSpan;
        $this->adaptiveColumnSpan = $adaptiveColumnSpan;

        return $this;
    }

    public function columnSpanValue(): int
    {
        return $this->columnSpan;
    }

    public function adaptiveColumnSpanValue(): int
    {
        return $this->adaptiveColumnSpan;
    }
}
