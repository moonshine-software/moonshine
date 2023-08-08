<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuComponent extends Component
{
    public function render(): View
    {
        return view('moonshine::components.menu.index', [
            'data' => moonshineMenu()->all(),
        ]);
    }
}
