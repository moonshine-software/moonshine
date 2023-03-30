<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Leeto\MoonShine\Menu\Menu;

class MenuComponent extends Component
{
    public function render(): View
    {
        $data = app(Menu::class)->all();

        return view('moonshine::components.menu.index', [
            'data' => $data,
        ]);
    }
}
