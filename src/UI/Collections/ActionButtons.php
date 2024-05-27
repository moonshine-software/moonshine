<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\ActionButtonContract;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, ActionButtonContract>
 */
final class ActionButtons extends Collection
{
    public function fillItem(mixed $item): self
    {
        return $this->map(
            fn (ActionButtonContract $action): ActionButtonContract => (clone $action)->setItem($item)
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
            fn (ActionButtonContract $action): bool => $action->isSee($item ?? $action->getItem())
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
