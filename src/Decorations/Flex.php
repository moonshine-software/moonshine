<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

class Flex extends Decoration
{
    protected string $view = 'moonshine::decorations.flex';

    protected string $itemsAlign = 'center';

    protected string $justifyAlign = 'center';

    protected bool $withoutSpace = false;

    public function withoutSpace(): self
    {
        $this->withoutSpace = true;

        return $this;
    }

    public function isWithoutSpace(): bool
    {
        return $this->withoutSpace;
    }

    public function itemsAlign(string $itemsAlign): self
    {
        $this->itemsAlign = $itemsAlign;

        return $this;
    }

    public function justifyAlign(string $justifyAlign): self
    {
        $this->justifyAlign = $justifyAlign;

        return $this;
    }

    public function getItemsAlign(): string
    {
        return $this->itemsAlign;
    }

    public function getJustifyAlign(): string
    {
        return $this->justifyAlign;
    }
}
