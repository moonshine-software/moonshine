<?php

namespace Leeto\MoonShine\Traits\Fields;

trait NumberTrait
{
    public int $min = 1;

    public int $max = 1000;

    public int $step = 1;

    public function min(int $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function step(int $step): static
    {
        $this->step = $step;

        return $this;
    }
}
