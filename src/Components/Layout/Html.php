<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

final class Html extends WithComponents
{
    protected string $view = 'moonshine::components.layout.html';

    public function withAlpineJs(): self
    {
        return $this->customAttributes([
            'x-data' => '',
        ]);
    }

    public function withThemes(): self
    {
        return $this->customAttributes([
            ':class' => "\$store.darkMode.on && 'dark'",
        ]);
    }
}
