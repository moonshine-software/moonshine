<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Leeto\PackageCommand\Command;

abstract class MoonShineCommand extends Command
{
    protected string $stubsDir = __DIR__ . '/../../../stubs';

    protected function getDirectory(): string
    {
        return moonshineConfig()->getDir();
    }

    protected function addResourceOrPageToProviderFile(string $class, bool $page = false): void
    {
        $file = app_path('Providers/MoonShineServiceProvider.php');

        if (! file_exists($file)) {
            return;
        }

        $method = $page ? 'pages' : 'resource';

        $content = file_get_contents($file);
        $block = str($content)
            ->betweenFirst("protected function $method(): array", '}');

        $tab = static fn(int $times = 1) => str_repeat(' ', 4 * $times);

        $content = str_replace(
            $block->value(),
            $block->replace("];", "{$tab()}\\$class::class,\n{$tab(2)}];")->value(),
            $content
        );

        file_put_contents($file, $content);
    }
}
