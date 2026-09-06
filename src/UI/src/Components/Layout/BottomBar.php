<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\AbstractWithComponents;

/**
 * @method static static make(iterable<array-key, ComponentContract> $components = [])
 */
class BottomBar extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.bottom-bar';

    protected array $translates = [
        'back' => 'moonshine::ui.back',
    ];

    public function alwaysVisible(): static
    {
        return $this->class('always-visible');
    }
}
