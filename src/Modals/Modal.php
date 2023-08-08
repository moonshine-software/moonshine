<?php

namespace MoonShine\Modals;

use Closure;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Traits\Makeable;

class Modal
{
    use Makeable;

    protected array $buttons = [];

    public function __construct(
        protected ?Closure $title,
        protected ?Closure $content
    ) {
    }

    public function title(mixed $data = null): ?string
    {
        return call_user_func($this->title, $data);
    }

    public function content(mixed $data = null): ?string
    {
        return call_user_func($this->content, $data);
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
