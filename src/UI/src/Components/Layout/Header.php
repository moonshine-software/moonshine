<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

class Header extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.header';

    protected array $translates = [
        'collapse_menu' => 'moonshine::ui.collapse_menu',
    ];
}
