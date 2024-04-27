<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\Contracts\Components\HasCanSeeContract;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Support\MoonShineComponentAttributeBag;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithViewRenderer;

abstract class MoonShineComponent extends Component implements MoonShineRenderable, HasCanSeeContract
{
    use Conditionable;
    use Macroable;
    use Makeable;
    use WithViewRenderer;
    use HasCanSee;
    use WithComponentAttributes;

    public function __construct(
        protected string $name = 'default'
    )
    {
        $this->attributes = new MoonShineComponentAttributeBag();
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function data(): array
    {
        return array_merge($this->extractPublicProperties(), [
            'type' => class_basename($this),
            'attributes' => $this->attributes(),
            'name' => $this->getName(),
        ]);
    }

    protected function systemViewData(): array
    {
        return $this->data();
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->systemViewData(),
            $this->viewData(),
            $this->getCustomViewData()
        );
    }
}
