<?php

namespace Leeto\MoonShine\Traits\Fields;

trait BooleanFieldTrait
{
    protected int|string $onValue = 1;

    protected int|string $offValue = 0;

    public function onValue($onValue): static
    {
        $this->onValue = $onValue;

        return $this;
    }

    public function getOnValue(): int|string
    {
        return (string) $this->onValue;
    }

    public function offValue($offValue): static
    {
        $this->offValue = $offValue;

        return $this;
    }

    public function getOffValue(): int|string
    {
        return (string) $this->offValue;
    }
}