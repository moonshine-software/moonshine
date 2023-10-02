<?php

namespace MoonShine\UI;

use Closure;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Traits\HasAsync;
use MoonShine\Traits\Makeable;

class Modal
{
    use Makeable;
    use HasAsync;

    protected array $buttons = [];

    public function __construct(
        protected ?Closure $title,
        protected ?Closure $content,
        bool $async = false
    ) {
        if ($async) {
            $this->async('#');
        }
    }

    public function title(mixed $data = null): ?string
    {
        return value($this->title, $data);
    }

    public function content(mixed $data = null): ?string
    {
        return value($this->content, $data);
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
