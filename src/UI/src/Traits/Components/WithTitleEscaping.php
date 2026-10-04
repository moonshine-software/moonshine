<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

use Illuminate\Contracts\Support\Htmlable;
use MoonShine\Support\DisplayHtml;

trait WithTitleEscaping
{
    protected ?bool $escapeTitle = null;

    public function escapeTitle(bool $escape = true): static
    {
        $this->escapeTitle = $escape;

        return $this;
    }

    public function unescapeTitle(): static
    {
        return $this->escapeTitle(false);
    }

    public function isEscapeTitle(): bool
    {
        return $this->escapeTitle ?? $this->getCore()->getConfig()->isEscapeLabel();
    }

    protected function getTitleHtml(mixed $title): Htmlable
    {
        return DisplayHtml::make($title, $this->isEscapeTitle());
    }
}
