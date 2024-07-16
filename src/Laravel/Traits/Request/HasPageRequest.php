<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Request;

use MoonShine\Contracts\Core\PageContract;

trait HasPageRequest
{
    public function findPage(): ?PageContract
    {
        return memoize(function (): ?PageContract {
            if(is_null($this->getPageUri())) {
                return null;
            }

            if ($this->hasResource()) {
                return $this->getResource()
                    ?->getPages()
                    ?->findByUri($this->getPageUri());
            }

            return moonshine()->getPages()->findByUri(
                $this->getPageUri()
            );
        });
    }

    public function getPage(): PageContract
    {
        $page = $this->findPage();

        if (is_null($page)) {
            oops404();
        }

        return $page;
    }

    public function getPageUri(): ?string
    {
        return $this->route('pageUri');
    }
}
