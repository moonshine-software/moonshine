<?php

declare(strict_types=1);

namespace MoonShine\ItemActions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\ItemActionContact;

final class ItemActions extends Collection
{
    public function onlyVisible(Model $item): self
    {
        return $this->filter(
            fn(ItemActionContact $action) => $action->isSee($item)
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (ItemActionContact $action) => $action->inDropdown()
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (ItemActionContact $action) => !$action->inDropdown()
        );
    }
}
