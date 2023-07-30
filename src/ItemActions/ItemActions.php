<?php

declare(strict_types=1);

namespace MoonShine\ItemActions;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\ItemActionContract;

final class ItemActions extends Collection
{
    public function fillItem(mixed $item): self
    {
        return $this->map(
            fn (ItemActionContract $action) => clone $action->setItem($item)
        );
    }

    public function onlyVisible(mixed $item): self
    {
        return $this->filter(
            fn (ItemActionContract $action) => $action->isSee($item)
        );
    }

    public function bulk(): self
    {
        return $this->filter(
            static fn (
                ItemActionContract $action
            ): bool => $action->isBulk()
        );
    }

    public function withoutBulk(): self
    {
        return $this->filter(
            static fn (
                ItemActionContract $action
            ): bool => ! $action->isBulk()
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (
                ItemActionContract $action
            ): bool => ! $action->inDropdown()
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (ItemActionContract $action) => $action->inDropdown()
        );
    }
}
