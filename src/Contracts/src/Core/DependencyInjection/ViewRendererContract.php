<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Illuminate\Contracts\Support\Renderable;

interface ViewRendererContract
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(string $view, array $data = []): Renderable;
}
