<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AsyncModal extends Component
{
    public function __construct(public string $id, public string $route)
    {
        $this->id = (string) str($id)->slug('_');
    }

    public function render(): View
    {
        return view('moonshine::components.async-modal');
    }
}
