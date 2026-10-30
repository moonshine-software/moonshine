<?php

declare(strict_types=1);

namespace MoonShine\Laravel\ViewComposers;

use Illuminate\View\ComponentSlot;
use Illuminate\View\View;

final class IconComposer
{
    public function compose(View $view): void
    {
        $slot = $view->getData()['slot'] ?? null;

        // Laravel supplies the Blade slot after the component has prepared its view data.
        if ($slot instanceof ComponentSlot) {
            $view->with('hasSlot', $slot->isNotEmpty());
        }
    }
}
