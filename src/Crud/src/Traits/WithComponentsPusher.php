<?php

declare(strict_types=1);

namespace MoonShine\Crud\Traits;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ComponentContract;

/**
 * @phpstan-ignore trait.unused
 */
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

    /**
     * @return list<ComponentContract>
     */
    protected function getPushedComponents(): array
    {
        return Collection::make(static::$pushedComponents)
            ->map(
                fn (Closure|ComponentContract $component) => $component instanceof Closure
                ? value($component, $this)
                : $component
            )
            ->toArray();
    }
}
