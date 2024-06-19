<?php

declare(strict_types=1);

namespace MoonShine\Core\Handlers;

use Illuminate\Support\Collection;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionButton;

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
            static fn (Handler $handler): bool => $handler->getUriKey() === $uri,
            $default
        );
    }

    public function getButtons(): ActionButtons
    {
        return ActionButtons::make(
            $this->map(static fn (Handler $handler): ActionButton => $handler->getButton())
        );
    }
}
