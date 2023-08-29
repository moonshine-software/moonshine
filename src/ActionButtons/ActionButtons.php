<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\ActionButtonContract;

/**
 * @template TKey of array-key
 *
 * @implements  Collection<TKey, ActionButtonContract>
 */
final class ActionButtons extends Collection
{
    public function fillItem(mixed $item): self
    {
        return $this->map(
            fn (ActionButtonContract $action) => (clone $action)->setItem($item)
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

    public function mergeIfNotExists(ActionButtonContract $new): self
    {
        return $this->when(
            ! $this->first(
                static fn (
                    ActionButtonContract $action
                ): bool => $action::class === $new::class
            ),
            static fn (
                self $actions
            ): self => $actions->add($new)
        );
    }

    public function onlyVisible(mixed $item = null): self
    {
        return $this->filter(
            fn (ActionButtonContract $action) => $action->isSee($item ?? moonshineRequest())
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
            static fn (ActionButtonContract $action): bool => $action->inDropdown()
        );
    }
}
