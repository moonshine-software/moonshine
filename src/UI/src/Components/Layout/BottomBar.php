<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

/**
 * @method static static make(iterable $components = [])
 */
class BottomBar extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.bottom-bar';

    protected array $translates = [
        'back' => 'moonshine::ui.back',
    ];

    public function __construct(
        iterable $components = [],
    ) {
        parent::__construct($components);
    }

    public function alwaysVisible(): static
    {
        return $this->class('always-visible');
    }
}
