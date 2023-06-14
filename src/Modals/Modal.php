<?php

namespace MoonShine\Modals;

use MoonShine\Traits\Makeable;

abstract class Modal
{
    use Makeable;

    protected ?string $confirmButtonText = null;

    public function __construct(
        protected ?string $title,
        protected ?string $content
    ) {}

    public function title(): ?string
    {
        return $this->title;
    }

    public function content(): ?string
    {
        return $this->content;
    }

    public function getConfirmButtonText(): ?string
    {
        return $this->confirmButtonText;
    }

    public function confirmButtonText(string $confirmButtonText): Modal
    {
        $this->confirmButtonText = $confirmButtonText;
        return $this;
    }
}