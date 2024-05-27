<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\Core\Contracts\Components\HasCanSeeContract;
use MoonShine\Core\Contracts\MoonShineRenderable;
use MoonShine\Support\Traits\HasCanSee;
use MoonShine\Support\Traits\Makeable;
use MoonShine\UI\Traits\Components\WithComponentAttributes;
use MoonShine\UI\Traits\WithViewRenderer;

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
    ) {
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
            'attributes' => $this->attributes(),
            'name' => $this->getName(),
        ]);
    }

    protected function systemViewData(): array
    {
        return $this->data();
    }
}
