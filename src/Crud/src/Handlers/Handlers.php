<?php

declare(strict_types=1);

namespace MoonShine\Crud\Handlers;

use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\UI\Collections\ActionButtons;

/**
 * @extends Collection<array-key, Handler>
 */
class Handlers extends Collection
{
    public function findByUri(
        string $uri,
        ?Handler $default = null
    ): ?Handler {
        return $this->first(
            static fn (Handler $handler): bool => $handler->getUriKey() === $uri,
            $default
        );
    }

    public function getButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->map(static fn (Handler $handler): ActionButtonContract => $handler->getButton())
        );
    }
}
