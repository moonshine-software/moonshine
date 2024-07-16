<?php

declare(strict_types=1);

namespace MoonShine\Core\Resources;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\ResourcesContract;
use MoonShine\Contracts\Core\ResourceContract;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, ResourceContract>
 */
final class Resources extends Collection implements ResourcesContract
{
    public function findByUri(
        string $uri,
        ResourceContract $default = null
    ): ?ResourceContract {
        return $this->first(
            static fn (ResourceContract $resource): bool => $resource->getUriKey() === $uri,
            $default
        );
    }

    public function findByClass(
        string $class,
        ResourceContract $default = null
    ): ?ResourceContract {
        return $this->first(
            static fn (ResourceContract $resource): bool => $resource::class === $class,
            $default
        );
    }
}
