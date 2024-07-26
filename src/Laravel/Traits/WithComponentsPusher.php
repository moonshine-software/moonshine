<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits;

use Closure;
use MoonShine\Contracts\Core\RenderableContract;

trait WithComponentsPusher
{
    /**
     * @var array<string, RenderableContract>
     */
    private static array $pushedComponents = [];

    public static function pushComponent(Closure|RenderableContract $component): void
    {
        static::$pushedComponents[] = $component;
    }

    private function getPushedComponents(): array
    {
        return collect(static::$pushedComponents)
            ->map(fn (Closure|RenderableContract $component) => value($component, $this))
            ->toArray();
    }
}
