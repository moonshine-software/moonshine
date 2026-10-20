<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Illuminate\Contracts\Support\Htmlable;
use MoonShine\Support\DisplayHtml;
use MoonShine\UI\Enums\DisplayTextType;
use MoonShine\UI\Traits\WithDisplayEscaping;

trait WithHint
{
    use WithDisplayEscaping;

    public function escapeHint(bool $escape = true): static
    {
        return $this->setDisplayEscaping(DisplayTextType::HINT, $escape);
    }

    public function unescapeHint(): static
    {
        return $this->escapeHint(false);
    }

    public function isEscapeHint(): bool
    {
        return $this->isDisplayEscaped(DisplayTextType::HINT);
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

    public function getHintHtml(): Htmlable
    {
        return DisplayHtml::make($this->getHint(), $this->isEscapeHint());
    }
}
