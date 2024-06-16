<?php

declare(strict_types=1);

namespace MoonShine\Core\Resources;

use Illuminate\Support\Collection;
use MoonShine\Core\Contracts\ResourceContract;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, ResourceContract>
 */
final class Resources extends Collection
{
    public function findByUri(
        string $uri,
        ResourceContract $default = null
    ): ?ResourceContract {
        return $this->first(
            fn (ResourceContract $resource): bool => $resource->getUriKey() === $uri,
            $default
        );
    }

    public function findByClass(
        string $class,
        ResourceContract $default = null
    ): ?ResourceContract {
        return $this->first(
            fn (ResourceContract $resource): bool => $resource::class === $class,
            $default
        );
    }
}
