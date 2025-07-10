<?php

declare(strict_types=1);

namespace MoonShine\Core\Pages;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\PagesContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Support\Enums\PageType;

/**
 * @template T of PageContract = PageContract
 *
 * @extends Collection<array-key, T>
 * @implements PagesContract<T>
 */
final class Pages extends Collection implements PagesContract
{
    public function setResource(ResourceContract $resource): Pages
    {
        return $this->each(static fn (PageContract $page): PageContract => $page->setResource($resource));
    }

    /**
     * @param null|T $default
     * @return null|T
     */
    public function findByType(
        PageType $type,
        ?PageContract $default = null
    ): ?PageContract {
        return $this->first(static fn (PageContract $page): bool => $page->getPageType() === $type, $default);
    }

    /**
     * @param class-string<T> $class
     * @param null|T $default
     * @return null|T
     */
    public function findByClass(
        string $class,
        ?PageContract $default = null
    ): ?PageContract {
        return $this->first(
            static fn (PageContract $page): bool => $page::class === $class,
            $default
        );
    }

    /**
     * @return null|T
     */
    public function indexPage(): ?PageContract
    {
        return $this->findByType(PageType::INDEX);
    }

    /**
     * @return null|T
     */
    public function formPage(): ?PageContract
    {
        return $this->findByType(PageType::FORM);
    }

    /**
     * @return null|T
     */
    public function detailPage(): ?PageContract
    {
        return $this->findByType(PageType::DETAIL);
    }

    /**
     * @return null|T
     */
    public function activePage(): ?PageContract
    {
        return $this->first(fn (PageContract $page): bool => $page->isActive());
    }

    /**
     * @param null|T $default
     * @return null|T
     */
    public function findByUri(
        string $uri,
        ?PageContract $default = null
    ): ?PageContract {
        return $this->first(
            static fn (PageContract $page): bool => $page->getUriKey() === $uri,
            $default
        );
    }
}
