<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Core\Pages\Page as CorePage;

abstract class Page extends CorePage
{
    protected function prepareRender(View|Closure|string $view): View|Closure|string
    {
        return $view->fragmentIf(
            moonshineRequest()->isFragmentLoad(),
            moonshineRequest()->getFragmentLoad()
        );
    }
}
