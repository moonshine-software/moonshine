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
        $withoutQuery = trim(parse_url($this->getUrl(), PHP_URL_PATH), '/');
        $currentPath = trim(moonshine()->getRequest()->getPath(), '/');

        if ($this->isCheckUrl() && ! str_contains($currentPath, $withoutQuery)) {
            oops404();
        }

        request()
            ?->route()
            ?->setParameter('pageUri', $this->getUriKey());
    }

    protected function prepareRender(Renderable|Closure|string $view): Renderable|Closure|string
    {
        return $view->fragmentIf(
            moonshineRequest()->isFragmentLoad(),
            moonshineRequest()->getFragmentLoad()
        );
    }
}
