<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Page;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

trait OverrideCrudPage
{
    protected function throw404(): void
    {
        oops404();
    }

    protected function throw403(): void
    {
        abort(403);
    }

    public function simulateRoute(?PageContract $page = null, ?ResourceContract $resource = null): static
    {
        $targetPage = $page ?? $this;
        $targetResource = $resource ?? $targetPage->getResource();

        request()
            ->route()
            ?->setParameter('pageUri', $targetPage->getUriKey());

        if (! \is_null($targetResource)) {
            $this->setResource($targetResource);

            request()
                ->route()
                ?->setParameter('resourceUri', $targetResource->getUriKey());
        }

        return $this;
    }
}
