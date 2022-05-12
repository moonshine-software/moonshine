<?php

namespace Leeto\MoonShine\Traits\Fields;

trait NumberFieldTrait
{
    public int $min = 1;

    public int $max = 1000;

    public int $step = 1;

    public string $fromField = 'from';

    public string $toField = 'to';

    public function fromField(string $fromField): static
    {
        $this->fromField = $fromField;

        return $this;
    }

    public function toField(string $toField): static
    {
        $this->toField = $toField;

        return $this;
    }

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