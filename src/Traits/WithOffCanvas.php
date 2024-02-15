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
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        bool $isLeft = false,
        bool $async = false,
        array $attributes = [],
    ): static {
        $this->offCanvas = OffCanvas::make($title, $content, $isLeft, $async)
            ->customAttributes($attributes);

        return $this;
    }

    public function offCanvas(): ?OffCanvas
    {
        return $this->offCanvas;
    }
}
