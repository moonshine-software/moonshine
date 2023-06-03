<?php

declare(strict_types=1);

namespace MoonShine\ItemActions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\ItemActionContract;

final class ItemActions extends Collection
{
    public function onlyVisible(Model $item): self
    {
        return $this->filter(
            fn (ItemActionContract $action) => $action->isSee($item)
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (ItemActionContract $action
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
