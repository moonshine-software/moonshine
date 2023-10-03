<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithView;
use Throwable;

abstract class MoonshineComponent extends Component implements MoonShineRenderable
{
    use Conditionable;
    use Macroable;
    use Makeable;
    use WithView;

    protected ?string $name = null;

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function customAttributes(array $attributes): static
    {
        if (! $this->attributes instanceof ComponentAttributeBag) {
            $this->attributes = $this->newAttributeBag($attributes);
        } else {
            $this->attributes = $this->attributes->merge($attributes);
        }

        return $this;
    }

    protected function viewData(): array
    {
        return [];
    }

    public function render(): View|Closure|string
    {
        $data = $this->viewData();

        return view(
            $this->getView(),
            [
                'attributes' => $this->attributes ?: $this->newAttributeBag(),
                'name' => $this->getName(),
            ] + $data
        );
    }

    /**
     * @throws Throwable
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }
}
