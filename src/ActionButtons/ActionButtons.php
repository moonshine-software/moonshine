<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\ActionButtonContract;

final class ActionButtons extends Collection
{
    public function fillItem(mixed $item): self
    {
        return $this->map(
            fn (ActionButtonContract $action) => clone $action->setItem($item)
        );
    }

    public function onlyVisible(mixed $item): self
    {
        return $this->filter(
            fn (ActionButtonContract $action) => $action->isSee($item)
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
            ): bool => !$action->isBulk()
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (
                ActionButtonContract $action
            ): bool => ! $action->inDropdown()
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (ActionButtonContract $action) => $action->inDropdown()
        );
    }
}
