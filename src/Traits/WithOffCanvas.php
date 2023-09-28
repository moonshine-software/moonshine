<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\UI\OffCanvas;

trait WithOffCanvas
{
    protected ?OffCanvas $offCanvas = null;

    public function isInOffCanvas(): bool
    {
        return ! is_null($this->offCanvas);
    }

    public function inOffCanvas(
        ?Closure $title = null,
        ?Closure $content = null,
        bool $isLeft = false
    ): static {
        $this->offCanvas = OffCanvas::make($title, $content, $isLeft);

        return $this;
    }

    public function offCanvas(): ?OffCanvas
    {
        return $this->offCanvas;
    }
}
