<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use MoonShine\Contracts\Actions\ActionContract;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;

abstract class Action implements ActionContract
{
    use Makeable;
    use WithView;
    use WithLabel;
    use HasCanSee;
    use InDropdownOrLine;

    protected ?ResourceContract $resource;

    final public function __construct(string $label)
    {
        $this->setLabel($label);
    }

    public function resource(): ?ResourceContract
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
