<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use MoonShine\MoonShine;
use Leeto\PackageCommand\Command;

class MoonShineCommand extends Command
{
    protected string $stubsDir = __DIR__ . '/../../stubs';

    protected function getDirectory(): string
    {
        return MoonShine::dir();
    }
}
