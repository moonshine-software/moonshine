<?php


namespace Leeto\MoonShine\Traits\Fields;

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

    public function addLink(string $name, string $link): static
    {
        $this->linkValue = $link;
        $this->linkName = $name;

        return $this;
    }
}