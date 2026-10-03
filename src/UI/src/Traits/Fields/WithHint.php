<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

trait WithHint
{
    protected ?bool $escapeHint = null;

    public function escapeHint(bool $escape = true): static
    {
        $this->escapeHint = $escape;

        return $this;
    }

    public function unescapeHint(): static
    {
        return $this->escapeHint(false);
    }

    public function isEscapeHint(): bool
    {
        return $this->escapeHint ?? $this->getCore()->getConfig()->isEscapeHint();
    }

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
