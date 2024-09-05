<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\UI\Components\MoonShineComponent;

final class Meta extends MoonShineComponent
{
    protected function resolveRender(): Renderable|Closure|string
    {
        return function (): string {
            if ($this->getAttributes()->has('name')) {
                return "<meta {$this->getAttributes()} />";
            }

            return "<meta name=\"{$this->getName()}\" {$this->getAttributes()} />";
        };
    }
}
