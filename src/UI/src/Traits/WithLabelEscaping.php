<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use MoonShine\UI\Enums\DisplayTextType;

trait WithLabelEscaping
{
    use WithDisplayEscaping;

    public function escapeLabel(bool $escape = true): static
    {
        return $this->setDisplayEscaping(DisplayTextType::LABEL, $escape);
    }

    public function unescapeLabel(): static
    {
        return $this->escapeLabel(false);
    }

    public function isEscapeLabel(): bool
    {
        return $this->isDisplayEscaped(DisplayTextType::LABEL);
    }

    public function hasLabelEscapingOverride(): bool
    {
        return $this->getDisplayEscaping(DisplayTextType::LABEL) !== null;
    }
}
