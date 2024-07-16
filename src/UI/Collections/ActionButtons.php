<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\ActionButtonsContract;
use MoonShine\Contracts\UI\ActionButtonContract;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, ActionButtonContract>
 */
final class ActionButtons extends Collection implements ActionButtonsContract
{
    public function fill(?CastedDataContract $item): self
    {
        return $this->map(
            static fn (ActionButtonContract $action): ActionButtonContract => (clone $action)->setData($item)
        );
    }

    public function bulk(?string $forComponent = null): self
    {
        return $this->filter(
            static fn (
                ActionButtonContract $action
            ): bool => $action->isBulk()
        )->map(static fn (ActionButtonContract $action) => $action->bulk($forComponent));
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
            static fn (ActionButtonContract $action): bool => $action->isSee($item ?? $action->getData()?->getOriginal())
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (
                ActionButtonContract $action
            ): bool => ! $action->isInDropdown()
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (ActionButtonContract $action): bool => $action->isInDropdown()
        );
    }
}
