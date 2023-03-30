<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Components;

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

    public function render(): View
    {
        return view('moonshine::components.async-modal');
    }
}
