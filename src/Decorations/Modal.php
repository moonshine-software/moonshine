<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Traits\WithIcon;

class Modal extends Decoration
{
    use WithIcon;

    protected static string $view = 'moonshine::decorations.modal';

    protected string $title = '';

    protected string $content = '';

    protected bool $isWide = false;

    protected bool $isAsync = false;

    protected ?string $asyncUrl = null;

    public function wide(): self
    {
        $this->isWide = true;

        return $this;
    }

    public function isWide(): bool
    {
        return $this->isWide;
    }

    public function async(string $url): self
    {
        $this->isAsync = true;
        $this->asyncUrl = $url;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function asyncUrl(): string
    {
        return $this->asyncUrl ?? '';
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
