<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\PageType;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, Page>
 */
final class Pages extends Collection
{
    public function setResource(ResourceContract $resource): Pages
    {
        return $this->each(fn (Page $page): Page => $page->setResource($resource));
    }

    public function findByType(
        PageType $type,
        Page $default = null
    ): ?Page {
        return $this->first(fn (Page $page): bool => $page->pageType() === $type, $default);
    }

    public function indexPage(): ?Page
    {
        return $this->findByType(PageType::INDEX);
    }

    public function formPage(): ?Page
    {
        return $this->findByType(PageType::FORM);
    }

    public function detailPage(): ?Page
    {
        return $this->findByType(PageType::DETAIL);
    }

    public function findByUri(
        string $uri,
        Page $default = null
    ): ?Page {
        return $this->first(
            function (Page $page) use ($uri): bool {
                if ($page->uriKey() === $uri) {
                    return true;
                }

                return
                    ! is_null($pageTypeUri = PageType::getTypeFromUri($uri))
                    && $page->pageType() === $pageTypeUri
                ;
            },
            $default
        );
    }
}
