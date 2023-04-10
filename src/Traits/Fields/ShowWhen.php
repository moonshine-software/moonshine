<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

trait ShowWhen
{
    public bool $showWhenState = false;

    public string $showWhenField;

    public string $showWhenValue;

    public function showWhen(string $field, string $value): static
    {
        $this->showWhenState = true;
        $this->showWhenField = $field;
        $this->showWhenValue = $value;

        return $this;
    }

    public function hasShowWhen(): bool
    {
        return $this->showWhenState;
    }
}
