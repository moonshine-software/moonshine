<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\Traits\Makeable;
use Stringable;

final class Raw implements AssetElement
{
    use Makeable;

    public function __construct(
        private readonly string $content,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toHtml(): string
    {
        return $this->getContent();
    }

    public function __toString(): string
    {
        return $this->getContent();
    }
}
