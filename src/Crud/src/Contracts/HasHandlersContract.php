<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts;

use MoonShine\Crud\Handlers\Handlers;

interface HasHandlersContract
{
    public function hasHandlers(): bool;

    public function getHandlers(): Handlers;
}
