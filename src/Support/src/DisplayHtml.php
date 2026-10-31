<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use JsonSerializable;

/**
 * Display text whose escaping has already been resolved.
 *
 * Blade outputs it as is, so nested components do not escape it twice,
 * while serialization keeps the prepared HTML as a plain string.
 */
final class DisplayHtml extends HtmlString implements JsonSerializable
{
    public static function make(mixed $value, bool $escape): self
    {
        if ($value instanceof Htmlable) {
            return new self($value->toHtml());
        }

        $value = Stringify::value($value);

        return new self($escape ? e($value) : $value);
    }

    public function jsonSerialize(): string
    {
        return $this->toHtml();
    }
}
