<?php

declare(strict_types=1);

namespace Leeto\MoonShine\RowActions;

use Leeto\MoonShine\MoonShineRouter;

final class ShowRowAction extends RowAction
{
    public function route(array $params = []): string
    {
        return MoonShineRouter::to('{uri}.show', $params);
    }
}
