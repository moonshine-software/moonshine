<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Collections\ComponentsCollection;
use MoonShine\Contracts\MoonShineRenderable;
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
        if(app()->runningUnitTests()) {
            $components = collect($components)
                ->map(fn (object $component): object => clone $component)
                ->toArray();
        }

        $this->components = $components;

        return $this;
    }
}
