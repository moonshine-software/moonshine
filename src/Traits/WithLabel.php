<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait WithLabel
{
    protected string $label = '';

    public function label(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
