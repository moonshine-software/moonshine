<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

class Collapse extends Decoration
{
    public static string $view = 'moonshine::decorations.collapse';

    protected bool $show = false;
    protected string $uniqid;

    public function __construct(...$arguments)
    {
        $this->setUniqId(uniqid());

        parent::__construct(...$arguments);
    }

    public function uniqid(): string
    {
        return 'collapse_' . $this->uniqid;
    }

    public function setUniqid(string $uniqid): static
    {
        $this->uniqid = $uniqid;

        return $this;
    }

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
