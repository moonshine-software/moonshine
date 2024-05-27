<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

trait HasDifferentHtmlTag
{
    protected string $tag = 'div';

    public function getTag(): string
    {
        return $this->tag;
    }

    public function tag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }
}
