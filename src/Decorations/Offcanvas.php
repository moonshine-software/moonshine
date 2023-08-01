<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Traits\WithIcon;

class Offcanvas extends Decoration
{
    use WithIcon;

    protected static string $view = 'moonshine::decorations.offcanvas';

    protected bool $isLeft = false;

    protected string $title = '';

    protected string $content = '';

    public function left(): self
    {
        $this->isLeft = true;

        return $this;
    }

    public function isLeft(): bool
    {
        return $this->isLeft;
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
