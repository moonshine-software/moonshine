<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Actions\MassActionContact;
use MoonShine\MoonShineRequest;

final class MassActions extends Collection
{
    public function mergeIfNotExists(MassActionContact $new): self
    {
        return $this->when(
            ! $this->first(static fn (Action $action) => get_class($action) === get_class($new)),
            static fn (MassActions $actions) => $actions->add($new)
        );
    }

    public function onlyVisible(): self
    {
        return $this->filter(
            static fn (MassActionContact $action) => $action->isSee(app(MoonShineRequest::class))
        );
    }

    public function inDropdown(): self
    {
        return $this->filter(
            static fn (MassActionContact $action) => $action->inDropdown()
        );
    }

    public function inLine(): self
    {
        return $this->filter(
            static fn (MassActionContact $action) => !$action->inDropdown()
        );
    }
}
