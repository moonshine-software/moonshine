<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

trait NumberTrait
{
    public int|float|null $min = null;

    public int|float|null $max = null;

    public int|float $step = 1;

    protected bool $stars = false;

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

    public function stars(): static
    {
        $this->stars = true;

        return $this;
    }

    public function withStars(): bool
    {
        return $this->stars;
    }
}
