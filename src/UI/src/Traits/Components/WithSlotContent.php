<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

use Closure;
use Illuminate\View\ComponentSlot;
use Stringable;

trait WithSlotContent
{
    protected Closure|string $content = '';

    public function content(Closure|string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSlot(): ComponentSlot
    {
        return new ComponentSlot(
            $this->stringifySlotContent(value($this->content, $this))
        );
    }

    private function stringifySlotContent(mixed $content): string
    {
        if ($content instanceof Stringable) {
            return (string) $content;
        }

        if (\is_scalar($content) || $content === null) {
            return (string) $content;
        }

        return '';
    }
}
