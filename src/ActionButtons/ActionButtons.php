<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use MoonShine\Actions\Actions;
use MoonShine\Contracts\Actions\ActionButtonContract;

final class ActionButtons extends Actions
{
    public function fillItem(mixed $item): self
    {
        return $this->map(
            fn (ActionButtonContract $action) => clone $action->setItem($item)
        );
    }

    public function bulk(): self
    {
        return $this->filter(
            static fn (
                ActionButtonContract $action
            ): bool => $action->isBulk()
        );
    }

    public function withoutBulk(): self
    {
        return $this->filter(
            static fn (
                ActionButtonContract $action
            ): bool => ! $action->isBulk()
        );
    }
}
