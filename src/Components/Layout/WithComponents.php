<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make(array|MoonShineRenderElements $components = [])
 */
abstract class WithComponents extends MoonShineComponent
{
    public function __construct(
        protected array|MoonShineRenderElements $components = [],
    ) {
    }

    protected function viewData(): array
    {
        return [
            'components' => $this->components,
        ];
    }
}
