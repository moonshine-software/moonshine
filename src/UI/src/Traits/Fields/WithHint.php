<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

trait WithHint
{
    protected string $hint = '';

    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint(): string
    {
        return $this->hint;
    }
}
