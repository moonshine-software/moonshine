<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Resources\ResourceContract;

/**
 * @template TKey of array-key
 *
 * @implements  Collection<TKey, Page>
 */
final class Pages extends Collection
{
    public function setResource(ResourceContract $resource): Pages
    {
        return $this->each(fn (Page $page): Page => $page->setResource($resource));
    }

    public function findByUri(
        string $uri,
        Page $default = null
    ): ?Page {
        return $this->first(
            static fn (Page $page): bool => $page->uriKey() === $uri,
            $default
        );
    }
}
