<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

use Illuminate\Contracts\Support\Htmlable;
use MoonShine\Support\DisplayHtml;
use MoonShine\UI\Enums\DisplayTextType;
use MoonShine\UI\Traits\WithDisplayEscaping;

trait WithTitleEscaping
{
    use WithDisplayEscaping;

    public function escapeTitle(bool $escape = true): static
    {
        return $this->setDisplayEscaping(DisplayTextType::TITLE, $escape);
    }

    public function unescapeTitle(): static
    {
        return $this->escapeTitle(false);
    }

    public function isEscapeTitle(): bool
    {
        return $this->isDisplayEscaped(DisplayTextType::TITLE);
    }

    protected function getTitleHtml(mixed $title): Htmlable
    {
        return DisplayHtml::make($title, $this->isEscapeTitle());
    }
}
