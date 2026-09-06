<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\AbstractWithComponents;

/**
 * @method static static make(iterable<array-key, ComponentContract> $components = [], int $gap = 6)
 */
class Grid extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.grid';

    public function __construct(
        iterable $components = [],
        private int $gap = 6
    ) {
        parent::__construct($components);
    }

    public function gap(int $value): static
    {
        $this->gap = $value;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            'gap' => $this->gap,
        ];
    }
}
