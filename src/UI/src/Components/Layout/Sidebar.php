<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\AbstractWithComponents;

class Sidebar extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.sidebar';

    protected array $translates = [
        'collapse_menu' => 'moonshine::ui.collapse_menu',
    ];

    public MoonShineComponentAttributeBag $collapseAttributes;

    public function __construct(
        iterable $components = [],
        public bool $collapsed = false
    ) {
        parent::__construct($components);

        $this->collapseAttributes = new MoonShineComponentAttributeBag();
    }

    public function collapsed(): static
    {
        $this->collapsed = true;

        return $this;
    }

    public function collapseAttributes(array $attributes): static
    {
        $this->collapseAttributes = $this->collapseAttributes->merge($attributes);

        return $this;
    }
}
