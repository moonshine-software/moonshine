<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\HtmlString;

/**
 * Trusted HTML usable where a Renderable is expected,
 * so contracts typed as Renderable|string can carry prepared markup.
 */
final class RenderableHtml extends HtmlString implements Renderable
{
    public static function wrap(Renderable|Htmlable|string $value): Renderable|string
    {
        return $value instanceof Htmlable && ! $value instanceof Renderable
            ? new self($value->toHtml())
            : $value;
    }

    public function render(): string
    {
        return $this->toHtml();
    }
}
