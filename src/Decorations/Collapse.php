<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

class Collapse extends Decoration
{
    public static string $view = 'moonshine::decorations.collapse';

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
