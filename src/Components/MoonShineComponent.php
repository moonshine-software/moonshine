<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithView;
use Throwable;

abstract class MoonShineComponent extends Component implements MoonShineRenderable, CanBeEscapedWhenCastToString
{
    use Conditionable;
    use Macroable;
    use Makeable;
    use WithView;
    use HasCanSee;

    protected $except = [
        'name',
    ];

    public function name(string $name): static
    {
        $this->componentName = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->componentName;
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

    public function attributes(): ComponentAttributeBag
    {
        return $this->attributes ?: $this->newAttributeBag();
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
                'attributes' => $this->attributes(),
                'name' => str_contains($this->getName() ?? '', '::')
                    ? 'default'
                    : $this->getName(),
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

    public function escapeWhenCastingToString($escape = true): self
    {
        return $this;
    }
}
