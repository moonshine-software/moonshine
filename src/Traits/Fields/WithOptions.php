<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait WithOptions
{
    protected array $options = [];

    public function options(array $data): static
    {
        $this->options = $data;

        return $this;
    }

    public function values(): array
    {
        return $this->options;
    }
}
