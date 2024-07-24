<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Core\Collections\Components;
use Throwable;

/**
 * @mixin RenderableContract
 */
trait WithComponents
{
    protected iterable $components = [];

    public function getPreparedComponents(): Components
    {
        if(! $this->components instanceof Components) {
            return Components::make($this->components);
        }

        return $this->components;
    }

    /**
     * @throws Throwable
     */
    public function getComponents(): Components
    {
        return $this->getPreparedComponents();
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
        if($this->getCore()->runningInConsole()) {
            $components = collect($components)
                ->map(static fn (object $component): object => clone $component)
                ->toArray();
        }

        $this->components = $components;

        return $this;
    }
}
