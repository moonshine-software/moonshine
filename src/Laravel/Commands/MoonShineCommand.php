<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Leeto\PackageCommand\Command;
use MoonShine\MenuManager\MenuItem;

abstract class MoonShineCommand extends Command
{
    protected string $stubsDir = __DIR__ . '/../../../stubs';

    protected function getDirectory(): string
    {
        return moonshineConfig()->getDir();
    }

    public static function addResourceOrPageToProviderFile(string $class, bool $page = false, string $prefix = ''): void
    {
        self::addResourceOrPageTo(
            $prefix . $class,
            append: "$class::class",
            to: app_path('Providers/MoonShineServiceProvider.php'),
            method: $page ? 'pages' : 'resources',
            page: $page
        );
    }

    public static function addResourceOrPageToMenu(string $class, string $title, bool $page = false, string $prefix = ''): void
    {
        self::addResourceOrPageTo(
            $prefix . $class,
            append: "MenuItem::make('{$title}', $class::class)",
            to: app_path('MoonShine/Layouts/MoonShineLayout.php'),
            method: 'menu',
            page: $page,
            use: MenuItem::class,
        );
    }

    private static function addResourceOrPageTo(string $class, string $append, string $to, string $method, bool $page, string $use = ''): void
    {
        if (! file_exists($to)) {
            return;
        }

        $dir = $page ? 'Pages' : 'Resources';
        $namespace = moonshineConfig()->getNamespace("\\$dir\\") . $class;

        $content = str(file_get_contents($to));

        if($content->contains($class)) {
            return;
        }

        $tab = static fn(int $times = 1): string => str_repeat(' ', $times * 4);

        $headSection = $content->before('class ');
        $resourcesSection = $content->betweenFirst("protected function $method(): array", '}');

        if($content->contains($use)) {
            $use = '';
        }

        $content = str_replace(
            [
                $headSection->value(),
                $resourcesSection->value(),
            ],
            [
                $headSection->replaceLast(';', (";\nuse $namespace;" . ($use ? "\nuse $use;" : ''))),
                $resourcesSection->replace("];", "{$tab()}$append,\n{$tab(2)}];")->value()
            ],
            $content->value()
        );

        file_put_contents($to, $content);
    }
}
