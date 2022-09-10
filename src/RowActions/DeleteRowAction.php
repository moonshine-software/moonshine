<?php

declare(strict_types=1);

namespace Leeto\MoonShine\RowActions;

use Leeto\MoonShine\MoonShineRouter;

final class DeleteRowAction extends RowAction
{
    public function resolveRoute(string $routeParam, string|int $primaryKey): static
    {
        $this->route = MoonShineRouter::to(
            str($routeParam)->plural().".destroy",
            [$routeParam => $primaryKey]
        );
        return $this;
    }
}
