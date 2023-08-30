<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonshineComponent;

class Menu extends MoonshineComponent
{
    protected string $view = 'moonshine::components.menu.index';
    protected function viewData(): array
    {
        return [
            'data' => moonshineMenu()->all(),
        ];
    }
}
