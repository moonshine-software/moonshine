<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Illuminate\Contracts\Support\Renderable;

interface ViewRendererContract
{
    public function render(string $view, array $data = []): Renderable|string;
}
