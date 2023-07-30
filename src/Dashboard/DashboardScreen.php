<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Renderable;

abstract class DashboardScreen
{
    public function getComponents(): Collection
    {
        $components = collect();

        collect($this->components() ?? [])->each(
            function ($component) use ($components): void {
                $component = is_string($component)
                    ? new $component()
                    : $component;

                if ($component instanceof Renderable) {
                    $components->add($component);
                }
            }
        );

        return $components;
    }

    abstract public function components(): array;
}
