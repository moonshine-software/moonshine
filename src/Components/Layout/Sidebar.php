<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\Collections\MoonShineRenderElements;

class Sidebar extends WithComponents
{
    protected string $view = 'moonshine::components.layout.sidebar';

    public bool $collapse = false;

    public ComponentAttributeBag $collapseAttributes;

    public function __construct(array|MoonShineRenderElements $components = [])
    {
        parent::__construct($components);

        $this->collapseAttributes = new ComponentAttributeBag();
    }

    public function collapsed(): self
    {
        $this->collapse = true;

        return $this;
    }

    public function collapseAttributes(array $attributes): self
    {
        $this->collapseAttributes = $this->collapseAttributes->merge($attributes);

        return $this;
    }
}
