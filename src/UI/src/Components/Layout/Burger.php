<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

final class Burger extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.burger';

    public string $toggleMethod = 'toggleSidebarMenu';

    public function toggleMethod(string $method): static
    {
        $this->toggleMethod = $method;

        return $this;
    }
}
