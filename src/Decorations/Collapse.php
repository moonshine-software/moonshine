<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

class Collapse extends Decoration
{
    protected string $view = 'moonshine::decorations.collapse';

    protected bool $show = false;

    protected bool $persist = false;

    public function show(bool $show = true): self
    {
        $this->show = $show;

        return $this;
    }

    public function isShow(): bool
    {
        return $this->show;
    }

    public function persist(bool $persist = true): self
    {
        $this->persist = $persist;

        return $this;
    }

    public function isPersist(): bool
    {
        return $this->persist;
    }
}
