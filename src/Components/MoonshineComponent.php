<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\View\Component;
use Throwable;

abstract class MoonshineComponent extends Component
{
    /**
     * @throws Throwable
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }
}