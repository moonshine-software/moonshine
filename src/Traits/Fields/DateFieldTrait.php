<?php

namespace Leeto\MoonShine\Traits\Fields;

trait DateFieldTrait
{
    protected string $format = 'Y-m-d H:i:s';

    public function format($format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}