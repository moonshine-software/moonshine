<?php

declare(strict_types=1);

namespace MoonShine\Traits\Request;

use MoonShine\Pages\Page;

trait HasPageRequest
{
    public function findPage(): ?Page
    {
        return memoize(function (): ?Page {
            if (is_null($this->getPageUri())) {
                return null;
            }

            if ($this->hasResource()) {
                return $this->getResource()
                    ?->getPages()
                    ?->findByUri($this->getPageUri());
            }

            return moonshine()->getPageFromUriKey(
                $this->getPageUri()
            );
        });
    }

    public function getPage(): Page
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
