<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

class Collapse extends Decoration
{
    protected string $view = 'moonshine::decorations.collapse';

    protected bool $show = false;

    public function show(bool $show = true): self
    {
        $this->show = $show;

        return $this;
    }

    public function isShow(): bool
    {
        return $this->show;
    }
}
