<?php

namespace MoonShine\Modals;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Traits\Makeable;

class Modal
{
    use Makeable;

    protected array $buttons = [];

    public function __construct(
        protected ?string $title,
        protected ?string $content
    ) {
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function content(): ?string
    {
        return $this->content;
    }

    public function buttons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons);
    }
}
