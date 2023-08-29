<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait InDropdownOrLine
{
    protected bool $inDropdown = false;

    public function inDropdown(): bool
    {
        return $this->inDropdown;
    }

    public function showInDropdown(): static
    {
        $this->inDropdown = true;

        return $this;
    }

    public function showInLine(): static
    {
        $this->inDropdown = false;

        return $this;
    }
}
