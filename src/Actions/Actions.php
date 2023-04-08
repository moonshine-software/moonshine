<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Illuminate\Support\Collection;
use Leeto\MoonShine\MoonShineRequest;

final class Actions extends Collection
{
    public function mergeIfNotExists(Action $new): self
    {
        return $this->when(
            ! $this->first(static fn (Action $action) => get_class($action) === get_class($new)),
            static fn (Actions $actions) => $actions->add($new)
        );
    }

    public function onlyVisible(): self
    {
        return $this->filter(
            fn (Action $action) => $action->isSee(app(MoonShineRequest::class))
        );
    }
}
