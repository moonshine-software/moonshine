<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Leeto\MoonShine\Contracts\Actions\ActionContract;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Traits\HasCanSee;
use Leeto\MoonShine\Traits\InDropdownOrLine;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithLabel;
use Leeto\MoonShine\Traits\WithView;

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
