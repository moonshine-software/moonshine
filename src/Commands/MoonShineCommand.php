<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Commands;

use Leeto\MoonShine\MoonShine;
use Leeto\PackageCommand\Command;

class MoonShineCommand extends Command
{
    protected string $stubsDir = __DIR__ . '/../../stubs';

    protected function getDirectory(): string
    {
        return MoonShine::dir();
    }
}
