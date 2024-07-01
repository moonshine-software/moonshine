<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use Closure;
use MoonShine\Core\Pages\Page as CorePage;
use Illuminate\Contracts\Support\Renderable;

abstract class Page extends CorePage
{
    protected function prepareRender(Renderable|Closure|string $view): Renderable|Closure|string
    {
        return $view->fragmentIf(
            moonshineRequest()->isFragmentLoad(),
            moonshineRequest()->getFragmentLoad()
        );
    }
}
