<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Pages\Page as CorePage;
use MoonShine\Crud\Contracts\WithResponseModifierContract;
use MoonShine\Crud\Exceptions\NotFoundException;
use MoonShine\Crud\Exceptions\UnauthorizedException;
use MoonShine\Crud\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template TResource of CrudResourceContract = CrudResourceContract
 * @template TCore of CoreContract = CoreContract
 *
 * @extends CorePage<TCore, TResource>
 */
abstract class Page extends CorePage implements WithResponseModifierContract
{
    protected function throw404(): void
    {
        throw new NotFoundException();
    }

    protected function throw403(): void
    {
        throw new UnauthorizedException();
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $withoutQuery = trim(parse_url($this->getUrl(), PHP_URL_PATH), '/');
        $currentPath = trim($this->getCore()->getRequest()->getPath(), '/');

        if ($this->isCheckUrl() && ! str_contains($currentPath, $withoutQuery)) {
            $this->throw404();
        }

        $this->simulateRoute();
    }

    /**
     * @param  PageContract<TCore, TResource>|null  $page
     * @param  TResource|null  $resource
     */
    public function simulateRoute(?PageContract $page = null, ?ResourceContract $resource = null): static
    {
        $targetPage = $page ?? $this;
        $targetResource = $resource ?? $targetPage->getResource();

        if (! \is_null($targetResource)) {
            $this->setResource($targetResource);
        }

        return $this;
    }

    protected function prepareRender(Renderable|Closure|string $view): Renderable|Closure|string
    {
        /** @var View $view */
        return $view->fragmentIf(
            $this->getCore()->getCrudRequest()->isFragmentLoad(),
            $this->getCore()->getCrudRequest()->getFragmentLoad(),
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
        $fragments = $this->getCore()->getCrudRequest()->getFragmentLoad();

        if ($fragments === null) {
            return null;
        }

        if (str_contains($fragments, ',')) {
            $fragments = explode(',', $fragments);
            $data = [];
            foreach ($fragments as $fragment) {
                [$selector, $name] = explode(':', $fragment);
                /** @var View $view */
                $view = $this->renderView();
                $data[$selector] = $view->fragment($name);
            }

            return JsonResponse::make()->html($data);
        }

        return null;
    }
}
