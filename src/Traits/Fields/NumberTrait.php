<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait NumberTrait
{
    public int|float $min = 1;

    public int|float $max = 1000;

    public int|float $step = 1;

    public function min(int|float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(int|float $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function step(int|float $step): static
    {
        $this->step = $step;

        return $this;
    }
}
