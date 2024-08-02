<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

trait WithEscapedValue
{
    protected bool $unescape = false;

    public function escape(): static
    {
        $this->unescape = false;

        return $this;
    }

    public function unescape(): static
    {
        $this->unescape = true;

        return $this;
    }

    public function isUnescape(): bool
    {
        return $this->unescape;
    }
}
