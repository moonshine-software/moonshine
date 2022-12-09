<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Closure;

trait LinkTrait
{
    protected string $linkValue = '';

    protected string $linkName = '';

    public function hasLink(): bool
    {
        return $this->getLinkValue() != '';
    }

    public function getLinkName(): string
    {
        return $this->linkName;
    }

    public function getLinkValue(): string
    {
        return $this->linkValue;
    }

    public function addLink(string $name, string|Closure $link): static
    {
        if(is_callable($link)) {
            $link = call_user_func($link);
        }

        $this->linkValue = $link;
        $this->linkName = $name;

        return $this;
    }
}
