<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Contracts\Resource;

use MoonShine\Laravel\Handlers\Handlers;

interface HasHandlersContract
{
    public function getHandlers(): Handlers;
}
