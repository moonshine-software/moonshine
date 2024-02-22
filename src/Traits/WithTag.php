<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithTag
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
