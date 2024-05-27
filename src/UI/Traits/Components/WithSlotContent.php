<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

use Closure;
use Illuminate\View\ComponentSlot;

trait WithSlotContent
{
    protected Closure|string $content = '';

    public function content(Closure|string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlot(): ComponentSlot
    {
        return new ComponentSlot(
            value($this->content, $this)
        );
    }
}
