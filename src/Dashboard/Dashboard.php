<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Renderable;
use MoonShine\MoonShine;

class Dashboard
{
    protected ?Collection $components = null;

    public function components(array $data): void
    {
        $this->components = collect();

        collect($data)->each(function ($component): void {
            $component = is_string($component)
                ? new $component()
                : $component;

            if ($component instanceof Renderable) {
                $this->components->add($component);
            }
        });
    }

    public function getComponents(): ?Collection
    {
        $class = MoonShine::namespace('\Dashboard');

        $components = class_exists($class)
            ? (new $class())->getComponents()
            : collect();

        return $this->components instanceof Collection
            ? $this->components
            : $components;
    }
}
