<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Support\Collection;

class Actions extends Collection
{
    public function mergeIfNotExists(AbstractAction $new): self
    {
        return $this->when(
            ! $this->first(
                static fn (
                    AbstractAction $action
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
            fn (AbstractAction $action) => $action->isSee($item ?? moonshineRequest())
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (
                AbstractAction $action
            ): bool => ! $action->inDropdown()
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (AbstractAction $action): bool => $action->inDropdown()
        );
    }
}
