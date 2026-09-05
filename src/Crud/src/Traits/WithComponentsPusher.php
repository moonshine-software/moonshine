<?php

declare(strict_types=1);

namespace MoonShine\Crud\Traits;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ComponentContract;

trait WithComponentsPusher
{
    /**
     * @var list<(Closure(static): ComponentContract)|ComponentContract>
     */
    protected static array $pushedComponents = [];

    /**
     * @param (Closure(static): ComponentContract)|ComponentContract $component
     */
    public static function pushComponent(Closure|ComponentContract $component): void
    {
        static::$pushedComponents[] = $component;
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getPushedComponents(): array
    {
        return array_values(Collection::make(static::$pushedComponents)
            ->map(
                fn (Closure|ComponentContract $component) => $component instanceof Closure
                ? value($component, $this)
                : $component
            )
            ->all());
    }
}
