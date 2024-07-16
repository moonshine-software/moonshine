<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\DependencyInjection\ViewRendererContract;

final class ViewRenderer implements ViewRendererContract
{
    public function render(string $view, array $data = []): Renderable|string
    {
        return view($view, $data);
    }
}
