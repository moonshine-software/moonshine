<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AsyncModal extends Component
{
    public function __construct(public string $route)
    {
    }

    public function render(): View
    {
        return view('moonshine::components.async-modal');
    }
}
