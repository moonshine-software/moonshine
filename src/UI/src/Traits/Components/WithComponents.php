<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Components;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Core\Collections\Components;
use Throwable;

/**
 * @mixin ComponentContract
 */
trait WithComponents
{
    protected iterable $components = [];

    protected ?Components $preparedComponents = null;

    public function resetPreparedComponents(): static
    {
        $this->preparedComponents = null;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getPreparedComponents(): Components
    {
        if (! \is_null($this->preparedComponents)) {
            return $this->preparedComponents;
        }

        return $this->preparedComponents = $this->prepareComponents();
    }

    /**
     * @throws Throwable
     */
    protected function prepareComponents(): Components
    {
        return $this->getComponents();
    }

    /**
     * @throws Throwable
     */
    public function getComponents(): Components
    {
        if (! $this->components instanceof Components) {
            return Components::make($this->components);
        }

        return $this->components;
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
        if ($this->getCore()->runningInConsole()) {
            $components = collect($components)
                ->map(static fn (object $component): object => clone $component)
                ->toArray();
        }

        $this->components = $components;

        return $this;
    }
}
