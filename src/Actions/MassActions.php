<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\MassActionContract;
use MoonShine\MoonShineRequest;

final class MassActions extends Collection
{
    public function mergeIfNotExists(MassActionContract $new): self
    {
        return $this->when(
            ! $this->first(
                static fn (Action $action
                ): bool => $action::class === $new::class
            ),
            static fn (MassActions $actions
            ): MassActions => $actions->add($new)
        );
    }

    public function onlyVisible(): self
    {
        return $this->filter(
            static fn (MassActionContract $action) => $action->isSee(
                app(MoonShineRequest::class)
            )
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (MassActionContract $action
            ): bool => ! $action->inDropdown()
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (MassActionContract $action) => $action->inDropdown()
        );
    }
}
