<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait Removable
{
    protected bool $removable = false;

    /**
     * Set field as removable
     *
     * @return $this
     */
    public function removable(): static
    {
        $this->removable = true;

        return $this;
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }
}
