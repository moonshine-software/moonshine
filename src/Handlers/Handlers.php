<?php

declare(strict_types=1);

namespace MoonShine\Handlers;

use Illuminate\Support\Collection;
use MoonShine\Components\ActionButtons\ActionButtons;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, Handler>
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

    public function getButtons(): ActionButtons
    {
        return ActionButtons::make(
            $this->map(fn(Handler $handler) => $handler->getButton())
        );
    }
}
