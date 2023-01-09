<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Components;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AsyncModal extends Component
{
    public string $id;

    public string $route;

    public function __construct($id, $route)
    {
        $this->id = (string) str($id)->slug('_');
        $this->route = $route;
    }

    public function render(): View|Factory|Htmlable|Closure|string|Application
    {
        return view('moonshine::components.async-modal');
    }
}
