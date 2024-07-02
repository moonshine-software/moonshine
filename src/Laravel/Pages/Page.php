<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Core\Pages\Page as CorePage;

abstract class Page extends CorePage
{
    protected function prepareBeforeRender(): void
    {
        $withoutQuery = parse_url($this->getUrl(), PHP_URL_PATH);

        if ($this->isCheckUrl() && trim($withoutQuery, '/') !== trim(moonshine()->getRequest()->getPath(), '/')) {
            oops404();
        }
    }

    protected function prepareRender(Renderable|Closure|string $view): Renderable|Closure|string
    {
        return $view->fragmentIf(
            moonshineRequest()->isFragmentLoad(),
            moonshineRequest()->getFragmentLoad()
        );
    }
}
