<?php

declare(strict_types=1);

namespace Leeto\MoonShine\RowActions;

use Leeto\MoonShine\MoonShineRouter;

final class EditRowAction extends RowAction
{
    public function route(array $params = []): string
    {
        return MoonShineRouter::to('{uri}.edit', $params);
    }
}
