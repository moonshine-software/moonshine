<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

/**
 * @method static static make(array $components = [])
 */
class LayoutBuilder extends WithComponents
{
    protected string $view = 'moonshine::components.layout.index';

    public function topMenuMode(): self
    {
        return $this->customAttributes([
            'class' => 'layout-wrapper--top-menu',
        ]);
    }
}
