<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

trait WithLabelEscaping
{
    protected ?bool $escapeLabel = null;

    public function escapeLabel(bool $escape = true): static
    {
        $this->escapeLabel = $escape;

        return $this;
    }

    public function unescapeLabel(): static
    {
        return $this->escapeLabel(false);
    }

    public function isEscapeLabel(): bool
    {
        return $this->escapeLabel ?? $this->getCore()->getConfig()->isEscapeLabel();
    }
}
