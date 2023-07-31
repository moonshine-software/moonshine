<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\ActionContract;
use MoonShine\MoonShineRequest;

final class Actions extends Collection
{
    public function mergeIfNotExists(ActionContract $new): self
    {
        return $this->when(
            ! $this->first(
                static fn (
                    ActionContract $action
                ): bool => $action::class === $new::class
            ),
            static fn (
                Actions $actions
            ): Actions => $actions->add($new)
        );
    }

    public function onlyVisible(): self
    {
        return $this->filter(
            static fn (ActionContract $action) => $action->isSee(
                app(MoonShineRequest::class)
            )
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (
                ActionContract $action
            ): bool => ! $action->inDropdown()
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (ActionContract $action) => $action->inDropdown()
        );
    }
}
