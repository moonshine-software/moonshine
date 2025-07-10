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
    /**
     * @var iterable<ComponentContract>
     */
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
            return new Components($this->components);
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

    /**
     * @param  iterable<array-key, ComponentContract>  $components
     *
     */
    public function setComponents(iterable $components): static
    {
        $this->components = $components;

        return $this;
    }
}
