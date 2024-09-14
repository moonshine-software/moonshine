<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits;

use Closure;
use MoonShine\Contracts\UI\ComponentContract;

trait WithComponentsPusher
{
    /**
     * @var array<string, Closure|ComponentContract>
     */
    protected static array $pushedComponents = [];

    public static function pushComponent(Closure|ComponentContract $component): void
    {
        static::$pushedComponents[] = $component;
    }

    protected function getPushedComponents(): array
    {
        return collect(static::$pushedComponents)
            ->map(
                fn (Closure|ComponentContract $component) => $component instanceof Closure
                ? value($component, $this)
                : $component
            )
            ->toArray();
    }
}
