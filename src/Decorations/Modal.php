<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Traits\HasAsync;
use MoonShine\Traits\WithIcon;

class Modal extends Decoration
{
    use WithIcon;
    use HasAsync;

    protected string $view = 'moonshine::decorations.modal';

    protected string $title = '';

    protected string $content = '';

    protected bool $isWide = false;

    public function wide(): self
    {
        $this->isWide = true;

        return $this;
    }

    public function isWide(): bool
    {
        return $this->isWide;
    }

    public function content(string $title, string $content): self
    {
        $this->title = $title;
        $this->content = $content;

        return $this;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
