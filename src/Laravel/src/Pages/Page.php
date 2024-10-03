<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Core\Pages\Page as CorePage;
use MoonShine\Laravel\Contracts\WithResponseModifierContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template TResource of CrudResourceContract
 * @extends CorePage<MoonShine, TResource>
 */
abstract class Page extends CorePage implements WithResponseModifierContract
{
    protected function prepareBeforeRender(): void
    {
        $withoutQuery = trim(parse_url($this->getUrl(), PHP_URL_PATH), '/');
        $currentPath = trim(moonshine()->getRequest()->getPath(), '/');

        if ($this->isCheckUrl() && ! str_contains($currentPath, $withoutQuery)) {
            oops404();
        }

        request()
            ->route()
            ?->setParameter('pageUri', $this->getUriKey());
    }

    protected function prepareRender(Renderable|Closure|string $view): Renderable|Closure|string
    {
        /**
         * @var View $view
         */
        return $view->fragmentIf(
            moonshineRequest()->isFragmentLoad(),
            moonshineRequest()->getFragmentLoad(),
        );
    }

    public function isResponseModified(): bool
    {
        return $this->modifyResponse() instanceof Response;
    }

    public function getModifiedResponse(): ?Response
    {
        return $this->isResponseModified() ? $this->modifyResponse() : null;
    }

    protected function modifyResponse(): ?Response
    {
        return null;
    }
}
