<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

class Fragment extends Decoration
{
    protected string $view = 'moonshine::decorations.fragment';

    protected ?string $name = null;

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name ?? $this->id();
    }
}
