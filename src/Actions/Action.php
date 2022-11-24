<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithView;

abstract class Action
{
    use Makeable;
    use WithView;

    protected string $label;

    protected ResourceContract|null $resource;

    final public function __construct(string $label)
    {
        $this->setLabel($label);
    }

    public function label(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function resource(): ResourceContract|null
    {
        return $this->resource;
    }

    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function render(): string
    {
        return view($this->getView(), [
            'action' => $this,
        ])->render();
    }
}
