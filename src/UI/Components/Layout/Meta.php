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
        return static fn (Meta $meta): string => "<meta {$meta->getAttributes()} />";
    }
}
