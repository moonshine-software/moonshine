<?php

declare(strict_types=1);

namespace Leeto\MoonShine\RowActions;

use Leeto\MoonShine\MoonShineRouter;

final class EditRowAction extends RowAction
{
    public function resolveRoute(string $routeParam, string|int $primaryKey): static
    {
        $this->route = MoonShineRouter::to(
            str($routeParam)->plural().".edit",
            [$routeParam => $primaryKey]
        );

        return $this;
    }
}
