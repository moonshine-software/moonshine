<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

use MoonShine\UI\Collections\ComponentsCollection;
use MoonShine\UI\Contracts\MoonShineRenderable;
use Throwable;

/**
 * @mixin MoonShineRenderable
 */
trait WithComponents
{
    protected iterable $components = [];

    public function preparedComponents(): ComponentsCollection
    {
        if(! $this->components instanceof ComponentsCollection) {
            return ComponentsCollection::make($this->components);
        }

        return $this->components;
    }

    /**
     * @throws Throwable
     */
    public function getComponents(): ComponentsCollection
    {
        return $this->preparedComponents();
    }

    /**
     * @throws Throwable
     */
    public function hasComponents(): bool
    {
        return $this->getComponents()->isNotEmpty();
    }

    public function setComponents(iterable $components): static
    {
        if(moonshine()->runningInConsole()) {
            $components = collect($components)
                ->map(fn (object $component): object => clone $component)
                ->toArray();
        }

        $this->components = $components;

        return $this;
    }
}
