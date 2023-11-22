<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;

abstract class WithComponents extends MoonShineComponent
{
    public function __construct(
        protected array $components = [],
    ) {
    }

    protected function viewData(): array
    {
        return [
            'components' => $this->components,
        ];
    }
}
