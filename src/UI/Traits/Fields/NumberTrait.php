<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

trait NumberTrait
{
    protected int|float|null $min = null;

    protected int|float|null $max = null;

    protected int|float $step = 1;

    protected bool $stars = false;

    public function min(int|float $min): static
    {
        $this->min = $min;
        $this->getAttributes()->set('min', (string) $this->min);

        return $this;
    }

    public function max(int|float $max): static
    {
        $this->max = $max;
        $this->getAttributes()->set('max', (string) $this->max);

        return $this;
    }

    public function step(int|float $step): static
    {
        $this->step = $step;
        $this->getAttributes()->set('step', (string) $this->step);

        return $this;
    }

    public function stars(): static
    {
        $this->stars = true;

        return $this;
    }

    public function isWithStars(): bool
    {
        return $this->stars;
    }
}
