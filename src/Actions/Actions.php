<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Leeto\MoonShine\MoonShineRequest;
use Throwable;
use Illuminate\Support\Collection;

final class Actions extends Collection
{
    public function mergeIfNotExists(Action $new): Actions
    {
        return $this->when(
            !$this->first(static fn(Action $action) => get_class($action) === get_class($new)),
            static fn(Actions $actions) => $actions->add($new)
        );
    }

    public function onlyVisible(): Actions
    {
        return $this->filter(
            fn (Action $action) => $action->isSee(app(MoonShineRequest::class))
        );
    }
}
