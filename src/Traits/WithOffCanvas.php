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

    // TODO Change to component in 3.0 and actions/default.blade will be simple
    public function inOffCanvas(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        bool $isLeft = false,
        bool $async = false,
        array $attributes = [],
        string $name = 'default'
    ): static {
        $this->offCanvas = OffCanvas::make($title, $content, $isLeft, $async)
            ->name($name)
            ->customAttributes($attributes);

        return $this;
    }

    public function offCanvas(): ?OffCanvas
    {
        return $this->offCanvas;
    }
}
