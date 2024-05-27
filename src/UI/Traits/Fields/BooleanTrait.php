<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

trait BooleanTrait
{
    protected int|string $onValue = 1;

    protected int|string $offValue = 0;

    public function onValue(int|string $onValue): static
    {
        $this->onValue = $onValue;

        return $this;
    }

    public function getOnValue(): int|string
    {
        return (string) $this->onValue;
    }

    public function offValue(int|string $offValue): static
    {
        $this->offValue = $offValue;

        return $this;
    }

    public function getOffValue(): int|string
    {
        return (string) $this->offValue;
    }
}
