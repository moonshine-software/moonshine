<?php

declare(strict_types=1);

namespace MoonShine\Core\Pages;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\PagesContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Support\Enums\PageType;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, PageContract>
 */
final class Pages extends Collection implements PagesContract
{
    public function setResource(ResourceContract $resource): Pages
    {
        return $this->each(static fn (PageContract $page): PageContract => $page->setResource($resource));
    }

    public function findByType(
        PageType $type,
        PageContract $default = null
    ): ?PageContract {
        return $this->first(static fn (PageContract $page): bool => $page->getPageType() === $type, $default);
    }

    public function findByClass(
        string $class,
        PageContract $default = null
    ): ?PageContract {
        return $this->first(
            static fn (PageContract $page): bool => $page::class === $class,
            $default
        );
    }

    public function indexPage(): ?PageContract
    {
        return $this->findByType(PageType::INDEX);
    }

    public function formPage(): ?PageContract
    {
        return $this->findByType(PageType::FORM);
    }

    public function detailPage(): ?PageContract
    {
        return $this->findByType(PageType::DETAIL);
    }

    public function findByUri(
        string $uri,
        PageContract $default = null
    ): ?PageContract {
        return $this->first(
            static function (PageContract $page) use ($uri): bool {
                if($page->getUriKey() === $uri) {
                    return true;
                }

                return
                    ! is_null($pageTypeUri = PageType::getTypeFromUri($uri))
                    && $page->getPageType() === $pageTypeUri
                ;
            },
            $default
        );
    }
}
