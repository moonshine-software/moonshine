<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;

trait LinkTrait
{
    protected string $linkValue = '';

    protected string $linkName = '';

    protected bool $linkBlank = false;

    public function hasLink(): bool
    {
        return $this->getLinkValue() !== '';
    }

    public function getLinkValue(): string
    {
        return $this->linkValue;
    }

    public function getLinkName(): string
    {
        return $this->linkName;
    }

    public function isLinkBlank(): bool
    {
        return $this->linkBlank;
    }

    public function addLink(
        string $name,
        string|Closure $link,
        bool $blank = false
    ): static {
        if (is_callable($link)) {
            $link = $link();
        }

        $this->linkValue = $link;
        $this->linkName = $name;
        $this->linkBlank = $blank;

        return $this;
    }
}
