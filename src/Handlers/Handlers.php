<?php

declare(strict_types=1);

namespace MoonShine\Handlers;

use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 *
 * @implements  Collection<TKey, Handler>
 */
final class Handlers extends Collection
{
    public function findByUri(
        string $uri,
        Handler $default = null
    ): ?Handler {
        return $this->first(
            static fn (Handler $handler): bool => $handler->uriKey() === $uri,
            $default
        );
    }
}
