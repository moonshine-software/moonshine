<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

trait WithHint
{
    protected string $hint = '';

    protected bool $hintRaw = false;

    public function hint(string $hint): static
    {
        $this->hint = $hint;
        $this->hintRaw = false;

        return $this;
    }

    public function hintHtml(string $hint): static
    {
        $this->hint = $hint;
        $this->hintRaw = true;

        return $this;
    }

    public function getHint(): string
    {
        return $this->hint;
    }

    public function isHintRaw(): bool
    {
        return $this->hintRaw;
    }
}
